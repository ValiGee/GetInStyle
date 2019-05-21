package com.example.getinstyle_login;

import android.content.Intent;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;

public class MainActivity extends AppCompatActivity {

    String site_ul;
    String client_id = "4";
    String client_secret = "g7OQnwMfenDQs0wk19ls5OOJfmjn7o4Eveh4lFYC";
    public static String access_token;
    EditText email, password;


    public void logInButtonOnClick(View view)
    {
        String site = site_ul + "/oauth/token";
        String current_action = "Login";
        String[] primele = new String[2];
        primele[0] = site;
        primele[1] = current_action;
        String urmatoarele[] = new String[14];
        urmatoarele[0] = "11";
        urmatoarele[1] = "grant_type";
        urmatoarele[2] = "password";
        urmatoarele[3] = "client_id";
        urmatoarele[4] = client_id;
        urmatoarele[5] = "client_secret";
        urmatoarele[6] = client_secret;
        urmatoarele[7] = "username";
        urmatoarele[8] = email.getText().toString();
        urmatoarele[9] = "password";
        urmatoarele[10] = password.getText().toString();
        urmatoarele[11] = "scope";
        urmatoarele[12] = "";
        new ATask().execute(primele, urmatoarele);
    }

    public void signUpButtonOnClick(View view){
        startActivity(new Intent(MainActivity.this, SignUpActivity.class));
    }

    public void testButtonOnClick(View view){
        startActivity(new Intent(MainActivity.this, LoadPictureForApplyStyle.class));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        email = (EditText) findViewById(R.id.email);
        password = (EditText) findViewById(R.id.password);
        site_ul = getApplicationContext().getResources().getString(R.string.site);
    }

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

        String ceva = "";
        @Override
        protected String doInBackground(String[]... urls) {

            try {
                String site = urls[0][0];
                Integer cate = Integer.parseInt(urls[1][0]);
                HashMap <String, String> hash = new HashMap<String, String>();
                for(int i = 1; i <= cate; i += 2)
                {
                    String a = urls[1][i];
                    String b = urls[1][i + 1];
                    Log.e("cineva", a);
                    Log.e("altcineva", b);
                    hash.put(a, b);
                }
                Log.e("rasp", site);
                URL obj = new URL(site);
                try {
                    Log.e("rasp", obj.toString());
                    HttpURLConnection con = (HttpURLConnection) obj.openConnection();
                    con.setRequestMethod("POST");
                    con.setRequestProperty("Content-Type",
                            "application/x-www-form-urlencoded");

                    con.setDoOutput(true);
                    OutputStream os = con.getOutputStream();
                    os.write(getPostDataString(hash).getBytes());
                    os.flush();
                    os.close();

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


                        try {

                            JSONObject obiect = new JSONObject(response.toString());
                            access_token = obiect.getString("access_token");
                            Log.e("asta e", access_token);
                            return "Logged in successfully!";

                        } catch (Throwable t) {
                            Log.e("My App", "Could not parse malformed JSON: \"");
                            return "There was a problem logging in!";
                        }

                    }
                    else
                    {
                        Log.e("rasp", "POST request not worked");
                        if(responseCode == 401)
                            return "Invalid credentials";
                        else
                            return "There was a problem logging in!";

                    }
                } catch (IOException e)
                {
                    e.printStackTrace();

                }
            }
            catch (MalformedURLException e)
            {
                Log.e("naspa", "E corupt!");

            }

            return ceva;

        }

        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            Toast.makeText(getApplicationContext(), result, Toast.LENGTH_LONG).show();
            if(result.equals("Logged in successfully!"))
                startActivity(new Intent(MainActivity.this, AllPhotoPage.class));
        }
    }
}
