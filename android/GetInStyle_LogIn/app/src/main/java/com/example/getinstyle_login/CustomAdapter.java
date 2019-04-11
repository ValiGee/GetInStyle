package com.example.getinstyle_login;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.AsyncTask;
import android.text.InputType;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.squareup.picasso.Picasso;


import java.util.ArrayList;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.Map;
import java.util.Queue;


/**
 * Created by vally on 12.02.2016.
 */
class CustomAdapter extends ArrayAdapter<ArrayList<String>> {

    Context contextRooms;
    CustomAdapter(Context context, ArrayList<ArrayList <String>> poze) {
        super(context, R.layout.activity_photo_page, poze);
        for(int i = 0; i < poze.size(); i++)
        {
            ArrayList <String> ceva = poze.get(i);
            Log.e("astea", ceva.get(1) + "da");
        }
        contextRooms = context;
    }
    String site = "http://10.11.1.64:8000/";


    static class ViewHolder {
        ImageView photo, like_button;
        TextView likes_count;
    }
    ViewHolder ceva;

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        final ViewHolder holder;
        View itemView = convertView;
        if (itemView == null) {
            itemView = LayoutInflater.from(contextRooms).inflate(R.layout.activity_photo_page, parent, false);
        }
        ArrayList<String> variabile = getItem(position);

            holder = new ViewHolder();
            holder.photo = (ImageView) itemView.findViewById(R.id.photo);
            holder.like_button = (ImageView) itemView.findViewById(R.id.like_button);
            holder.likes_count = (TextView) itemView.findViewById(R.id.likes_count);

            //setat valori
            Picasso.get().load(site + variabile.get(0)).into(holder.photo);
            holder.likes_count.setText(variabile.get(1));

            itemView.setTag(holder);

        return itemView;
    }


    /* Va trebui sa o folosim cand bagam si like
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
    }

    public class ATask extends AsyncTask<String[], Void, String> {
        String altceva = "";
        ViewHolder myHolder;
        public ATask(ViewHolder view) {
            myHolder = view;
            ceva = myHolder;
            room_id = myHolder.room_id;
        }
        @Override
        protected String doInBackground(String[]... urls) {
            //try {
            try {
                String site = urls[0][0];
                String current_action = urls[0][1];
                Integer cate = Integer.parseInt(urls[1][0]);
                HashMap<String, String> hash = new HashMap<String, String>();
                for(int i = 1; i <= cate; i += 2)
                {
                    String a = urls[1][i];
                    String b = urls[1][i + 1];
                    hash.put(a, b);
                    Log.e("bola", a + b);
                }
                Log.e("rasp", site);
                URL obj = new URL(site);
                try {
                    Log.e("rasp", obj.toString());
                    HttpURLConnection con = (HttpURLConnection) obj.openConnection();
                    con.setRequestMethod("POST");
                    con.setRequestProperty("Content-Type",
                            "application/x-www-form-urlencoded");
                    //con.setRequestProperty("User-Agent", USER_AGENT);
                    // For POST only - START
                    con.setDoOutput(true);
                    OutputStream os = con.getOutputStream();
                    os.write(getPostDataString(hash).getBytes());
                    os.flush();
                    os.close();
                    // For POST only - END
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
                        coada.add(current_action);
                        coada2.add(response.toString());
                        coada3.add(true);
                    }
                    else
                    {
                        Log.e("rasp", "POST request not worked");
                        coada3.add(false);
                    }
                } catch (IOException e)
                {
                    e.printStackTrace();
                    coada3.add(false);
                }
            }
            catch (MalformedURLException e)
            {
                Log.e("naspa", "E corupt!");
                coada3.add(false);
            }
            //} catch (Exception e) {
            // Log.e("rasp", "aia e");
            //}
            return altceva;
        }
        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            Boolean success = coada3.poll();
            if(success)
            {
                String actiune = coada.poll();
                String rezultat = coada2.poll();
                if(!rezultat.equals("Wrong room!"))
                {
                    if(actiune.equals("CheckPass"))
                    {
                        if(rezultat.equals("OK"))
                        {
                            String site = site_ul + "/checkstart";
                            String current_action = "CheckStart";
                            String[] primele = new String[2];
                            primele[0] = site;
                            primele[1] = current_action;
                            String urmatoarele[] = new String[3];
                            urmatoarele[0] = "1";
                            urmatoarele[1] = "room";
                            urmatoarele[2] = myHolder.room_id;
                            new ATask(myHolder).execute(primele, urmatoarele);
                        }
                        else
                            Toast.makeText(contextRooms, rezultat, Toast.LENGTH_LONG).show();
                    }
                    else if(actiune.equals("CheckStart"))
                    {
                        if(rezultat.equals("Nu"))
                        {
                            Intent incercare = new Intent(contextRooms, BeforeGame.class);
                            incercare.putExtra("nume", Rooms.name);
                            incercare.putExtra("room", myHolder.room_id);
                            incercare.putExtra("room_name", myHolder.camera.getText().toString());
                            incercare.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                            contextRooms.startActivity(incercare);
                        }
                        else
                            Toast.makeText(contextRooms, "Jocul a inceput deja!", Toast.LENGTH_LONG).show();
                    }
                }
                else
                {
                    Toast.makeText(contextRooms, "Camera nu mai exista!", Toast.LENGTH_LONG).show();
                }
            }
            else
                Toast.makeText(contextRooms, "Eroare la conexiunea la server!", Toast.LENGTH_LONG).show();
        }
    }
    */
}
