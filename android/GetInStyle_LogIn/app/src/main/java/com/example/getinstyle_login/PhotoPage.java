package com.example.getinstyle_login;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.squareup.picasso.Picasso;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class PhotoPage extends AppCompatActivity {

    private LinearLayout linearLayout;
    ImageView photo, likes_button;
    TextView likes_count, description;

    String image_id;
    String site;
    String raspuns;
    Boolean liked = false;
    List <String> taguri = new ArrayList<String>();

    public static void setMargins(View v, int left, int top, int right, int bottom) {
        if (v.getLayoutParams() instanceof ViewGroup.MarginLayoutParams) {
            ViewGroup.MarginLayoutParams p = (ViewGroup.MarginLayoutParams) v.getLayoutParams();
            p.setMargins(left, top, right, bottom);
            v.requestLayout();
        }
    }

    public void setTags(List<String> tags) {
        for (String tag : tags) {
            final TextView textView = new TextView(this);
            textView.setLayoutParams(new LinearLayout.LayoutParams(
                ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.MATCH_PARENT)); // value is in pixels
            textView.setBackgroundColor(getResources().getColor(R.color.colorAccent));
            textView.setText(tag);
            textView.setPadding(10, 10, 10, 10);
            textView.setTextColor(getResources().getColor(R.color.colorPrimaryLight));
            textView.setTextSize(30);
            PhotoPage.setMargins(textView, 10, 10, 10, 10);
            if (linearLayout != null) {
                linearLayout.addView(textView);
            }
        }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_photo_page);

        site = getResources().getString(R.string.site);
        linearLayout = (LinearLayout) findViewById(R.id.tags_container);
        photo = findViewById(R.id.photo);
        //taguri =
        likes_count = findViewById(R.id.likes_count);
        likes_button = findViewById(R.id.like_button);
        description = findViewById(R.id.description);
        Intent intent = getIntent();
        image_id = intent.getStringExtra("image_id");

        new ATask().execute(image_id);

        //Picasso.get().load().into(photo);

        //setTags(Arrays.asList("#style", "#colors", "#hola", "#chicos")); // TO DO
    }

    public void like(View view)
    {
        new ATask2().execute(image_id);
    }


    public class ATask extends AsyncTask<String, Void, String> {

        String ceva = "";

        @Override
        protected String doInBackground(String... id) {

            try {
                Log.e("rasp", site);
                URL obj = new URL(site + "/api/media/" + id[0]);
                try {
                    Log.e("rasp", obj.toString());
                    HttpURLConnection con = (HttpURLConnection) obj.openConnection();
                    con.setRequestMethod("GET");
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

                        raspuns = response.toString();
                        return "OK";


                    } else {
                        Log.e("rasp", "POST request not worked");
                        return "There was a problem getting the data from the server!";

                    }
                } catch (IOException e) {
                    e.printStackTrace();

                }
            } catch (MalformedURLException e) {
                Log.e("naspa", "E corupt!");
                return "There was a problem connecting to the site!";
            }
            return "Unknown error!";
        }

        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            if (!result.equals("OK"))
                Toast.makeText(getApplicationContext(), result, Toast.LENGTH_LONG).show();
            else {
                try {
                    Log.e("raspunsul", raspuns);
                    JSONObject obiect = new JSONObject(raspuns);
                    obiect = obiect.getJSONObject("media");
                    String image_path = site + "/" + obiect.getString("stylized_path");
                    String likes_count_text = Integer.toString(obiect.getInt("likes_count"));
                    String liked_text= Integer.toString(obiect.getInt("liked"));
                    String description_text = obiect.getString("description");
                    JSONArray tags = obiect.getJSONArray("tags");
                    for(int i = 0; i < tags.length(); i++)
                        taguri.add(tags.getJSONObject(i).getString("name"));
                    setTags(taguri);
                    Picasso.get().load(image_path).into(photo);
                    likes_count.setText(likes_count_text);
                    if(!description_text.equals("null")) {
                        description.setVisibility(View.VISIBLE);
                        description.setText(description_text);
                    }
                    if(!liked_text.equals("0")) {
                        liked = true;
                        likes_button.setImageResource(R.drawable.ic_thumb_up_blue_24dp);
                    }


                } catch (Throwable t) {
                    Log.e("Eroare JSON", t.getMessage());
                }
            }
        }
    }
    public class ATask2 extends AsyncTask<String, Void, String> {

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
                Toast.makeText(getApplicationContext(), result, Toast.LENGTH_LONG).show();
            else {
                if (liked == false) {
                    Log.e("ceva", "da");
                    likes_count.setText(Integer.toString(Integer.parseInt(likes_count.getText().toString()) + 1));
                    likes_button.setImageResource(R.drawable.ic_thumb_up_blue_24dp);
                    liked = true;
                } else {
                    Log.e("ceva", "nu");
                    likes_count.setText(Integer.toString(Integer.parseInt(likes_count.getText().toString()) - 1));
                    likes_button.setImageResource(R.drawable.ic_thumb_up_white_24dp);
                    liked = false;
                }
            }
        }
    }
}
