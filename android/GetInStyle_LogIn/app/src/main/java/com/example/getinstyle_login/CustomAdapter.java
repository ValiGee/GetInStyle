package com.example.getinstyle_login;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.squareup.picasso.Picasso;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;


/**
 * Created by vally on 12.02.2016.
 */
class CustomAdapter extends ArrayAdapter<ArrayList<String>> {

    Context contextRooms;
    String site = this.getContext().getResources().getString(R.string.site);
    ViewHolder ceva;


    CustomAdapter(Context context, ArrayList<ArrayList<String>> poze) {
        super(context, R.layout.activity_photo_page, poze);
        for (int i = 0; i < poze.size(); i++) {
            ArrayList<String> ceva = poze.get(i);
            Log.e("astea", ceva.get(1) + "da");
        }
        contextRooms = context;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        final ViewHolder holder;
        View itemView = convertView;
        if (itemView == null) {
            itemView = LayoutInflater.from(contextRooms).inflate(R.layout.activity_photo_page, parent, false);
        }
        final ArrayList<String> variabile = getItem(position);

        holder = new ViewHolder();
        holder.photo = (ImageView) itemView.findViewById(R.id.photo);
        holder.like_button = (ImageView) itemView.findViewById(R.id.like_button);
        holder.likes_count = (TextView) itemView.findViewById(R.id.likes_count);


        //setat valori
        Picasso.get().load(site + "/" + variabile.get(0)).into(holder.photo);

        holder.likes_count.setText(variabile.get(1));
        holder.id = variabile.get(2);
        if(variabile.get(3).equals("0")) {
            holder.liked = false;
            holder.like_button.setImageResource(R.drawable.ic_thumb_up_white_24dp);
        }
        else {
            Log.e("alta poza", "  " + holder.id);
            holder.liked = true;
            holder.like_button.setImageResource(R.drawable.ic_thumb_up_blue_24dp);
        }

        holder.photo.setOnClickListener(new View.OnClickListener() {
            public void onClick(final View v) {
                Intent sharingIntent = new Intent(contextRooms, PhotoPage.class);
                sharingIntent.setType("text/plain");
                sharingIntent.putExtra("image_id",holder.id);
                contextRooms.startActivity(sharingIntent);
            }
        });

        holder.like_button.setOnClickListener(new View.OnClickListener() {
            public void onClick(final View v) {
                new ATask((ViewHolder) v.getTag()).execute(holder.id);
            }
        });

        itemView.setTag(holder);
        holder.like_button.setTag(holder);

        return itemView;
    }

    static class ViewHolder {
        ImageView photo, like_button;
        TextView likes_count;
        String id;
        boolean liked = false;
    }

    /*
    private String getPostDataString(HashMap<String, String> params) throws UnsupportedEncodingException {
        StringBuilder result = new StringBuilder();
        boolean first = true;
        for(Map.Entry<String, String> entry : params.entrySet()){
            if (first)
                first = false;
            else
                result.append("&");

            result.append(URLEncoder.encode(entry.getKey(), "UTF-8"));
            result.append("=");
            result.append(URLEncoder.encode(entry.getValue(), "UTF-8"));
        }
        return result.toString();
    }*/

    public class ATask extends AsyncTask<String, Void, String> {
        ViewHolder myHolder;

        public ATask(ViewHolder view) {
            myHolder = view;
            ceva = myHolder;
        }

        @Override
        protected String doInBackground(String... id) {
            //try {
            try {
                String site_ul = site + "/api/media/" + id[0] + "/like";
                Log.e("rasp", site_ul);
                URL obj = new URL(site_ul);
                try {
                    Log.e("rasp", obj.toString());
                    HttpURLConnection con = (HttpURLConnection) obj.openConnection();
                    con.setRequestMethod("POST");
                    con.setRequestProperty("Content-Type",
                        "application/x-www-form-urlencoded");
                    con.setRequestProperty("Authorization",
                        "Bearer " + MainActivity.access_token);
                    con.setRequestProperty("Accept",
                        "application/json");

                    int responseCode = con.getResponseCode();
                    Log.e("rasp", "response code-ul e " + Integer.toString(responseCode));
                    if (responseCode == HttpURLConnection.HTTP_OK) { //success
                        BufferedReader in = new BufferedReader(new InputStreamReader(
                            con.getInputStream()));
                        String inputLine;
                        StringBuffer response = new StringBuffer();
                        while ((inputLine = in.readLine()) != null) {
                            response.append(inputLine);
                        }
                        in.close();
                        // print result
                        Log.e("raspuns", response.toString());
                        return "OK";
                    } else {
                        Log.e("rasp", "POST request not worked");
                        return "There was a problem communicating with the server!";
                    }
                } catch (IOException e) {
                    e.printStackTrace();
                }
            } catch (MalformedURLException e) {
                Log.e("naspa", "E corupt!");
                return "There was a problem connecting to the server!";
            }

            return "Unknown error!";
        }

        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            if (!result.equals("OK"))
                Toast.makeText(contextRooms, result, Toast.LENGTH_LONG).show();
            else {
                if (myHolder.liked == false) {
                    Log.e("ceva", "da");
                    myHolder.likes_count.setText(Integer.toString(Integer.parseInt(myHolder.likes_count.getText().toString()) + 1));
                    myHolder.like_button.setImageResource(R.drawable.ic_thumb_up_blue_24dp);
                    myHolder.liked = true;
                } else {
                    Log.e("ceva", "nu");
                    myHolder.likes_count.setText(Integer.toString(Integer.parseInt(myHolder.likes_count.getText().toString()) - 1));
                    myHolder.like_button.setImageResource(R.drawable.ic_thumb_up_white_24dp);
                    myHolder.liked = false;
                }
            }
        }
    }

}
