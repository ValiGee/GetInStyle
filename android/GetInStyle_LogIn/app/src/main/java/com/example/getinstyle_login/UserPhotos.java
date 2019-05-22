package com.example.getinstyle_login;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.NavigationView;
import android.support.design.widget.Snackbar;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.Spinner;
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
import java.util.List;

public class UserPhotos extends AppCompatActivity {

    String site;
    String raspuns;
    String avatar_raspuns;
    ImageView avatar_img;
    TextView nume_text, email_text;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_all_photo_page);
        site = getApplicationContext().getResources().getString(R.string.site);

        new ATask().execute();
    }

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            super.onBackPressed();
        }
    }


    public class ATask extends AsyncTask<String[], Void, String> {

        String ceva = "";

        @Override
        protected String doInBackground(String[]... urls) {

            try {
                Log.e("rasp", site);
                URL obj = new URL(site + "/api/my_photos");
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
                    JSONArray poze = new JSONArray(raspuns);
                    List<ArrayList<String>> pozele = new ArrayList<ArrayList<String>>();
                    for (int i = 0; i < poze.length(); i++) {
                        List<String> poza = new ArrayList<String>();
                        poza.add(poze.getJSONObject(i).getString("stylized_path"));
                        poza.add(Integer.toString(poze.getJSONObject(i).getInt("likes_count")));
                        poza.add(Integer.toString(poze.getJSONObject(i).getInt("id")));
                        poza.add(Integer.toString(poze.getJSONObject(i).getInt("liked")));
                        Log.e("poza", Integer.toString(poze.getJSONObject(i).getInt("id")) + "  " + Integer.toString(poze.getJSONObject(i).getInt("liked")));
                        pozele.add(new ArrayList<String>(poza));
                    }

                    ListAdapter myAdapter = new CustomAdapter(UserPhotos.this, new ArrayList<>(pozele));
                    ListView lista = findViewById(R.id.lista);
                    lista.setAdapter(myAdapter);

                } catch (Throwable t) {
                    Log.e("Eroare JSON", t.getMessage());
                }
            }
        }
    }
}
