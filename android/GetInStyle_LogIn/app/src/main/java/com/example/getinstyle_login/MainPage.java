package com.example.getinstyle_login;

import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class MainPage extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main_page);
        site = getApplicationContext().getResources().getString(R.string.site);
        new ATask().execute();
    }

    String site;
    String raspuns;

    public class ATask extends AsyncTask<String[], Void, String> {

        String ceva = "";
        @Override
        protected String doInBackground(String[]... urls) {

            try {
                Log.e("rasp", site);
                URL obj = new URL(site);
                try {
                    Log.e("rasp", obj.toString());
                    HttpURLConnection con = (HttpURLConnection) obj.openConnection();
                    con.setRequestMethod("GET");
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

                        raspuns= response.toString();
                        return "OK";


                    }
                    else
                    {
                        Log.e("rasp", "POST request not worked");
                        return "There was a problem getting the data from the server!";

                    }
                } catch (IOException e)
                {
                    e.printStackTrace();

                }
            }
            catch (MalformedURLException e)
            {
                Log.e("naspa", "E corupt!");
                return "There was a problem connecting to the site!";
            }
            return "Unknown error!";
        }

        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            if(!result.equals("OK"))
                Toast.makeText(getApplicationContext(), result, Toast.LENGTH_LONG).show();
            else
            {
                try {
                    Log.e("raspunsul", raspuns);
                    JSONArray poze = new JSONArray(raspuns);
                    List<ArrayList<String>> pozele = new ArrayList<ArrayList<String>>();
                    for(int i = 0; i < poze.length(); i++)
                    {
                        List<String> poza = new ArrayList<String>();
                        poza.add(poze.getJSONObject(i).getString("path"));
                        poza.add(Integer.toString(poze.getJSONObject(i).getInt("likes_count")));
                        poza.add(Integer.toString(poze.getJSONObject(i).getInt("id")));
                        pozele.add(new ArrayList<String>(poza));
                    };

                    ListAdapter myAdapter = new CustomAdapter(MainPage.this, new ArrayList<>(pozele));
                    ListView lista = findViewById(R.id.lista);
                    lista.setAdapter(myAdapter);

                } catch (Throwable t) {
                    Log.e("Eroare JSON", t.getMessage());
                }
            }
        }
    }
}
