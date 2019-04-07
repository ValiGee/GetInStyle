package com.example.getinstyle_login;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.Toast;

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

public class SignUpActivity extends AppCompatActivity {

    public void testButtonOnClick(View view){
        startActivity(new Intent(SignUpActivity.this, LoadPictureForApplyStyle.class));
    }

    EditText email, name, password, confirm_password;
    private ImageView imageView;
    public static final int GALLERY_REQUEST_CODE = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_signup);
        email = (EditText) findViewById(R.id.email);
        name = (EditText) findViewById(R.id.name);
        password = (EditText) findViewById(R.id.password);
        confirm_password = (EditText) findViewById(R.id.confirm_password);
        imageView = (ImageView) findViewById(R.id.styleimageview);
        imageView.setVisibility(View.GONE);
    }

    public void selectImage(View view){
        pickFromGallery();
    }

    private void pickFromGallery(){
        //Create an Intent with action as ACTION_PICK
        Intent intent=new Intent(Intent.ACTION_PICK);
        // Sets the type as image/*. This ensures only components of type image are selected
        intent.setType("image/*");
        //We pass an extra array with the accepted mime types. This will ensure only components with these MIME types as targeted.
        String[] mimeTypes = {"image/jpeg", "image/png"};
        intent.putExtra(Intent.EXTRA_MIME_TYPES,mimeTypes);
        // Launching the Intent
        startActivityForResult(intent,GALLERY_REQUEST_CODE);
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

    String site_ul = "http://192.168.0.112:8000";

    public void createAccountOnClick(View view)
    {
        if(password.getText().toString().equals(confirm_password.getText().toString())) {
            String site = site_ul + "/api/register";
            String current_action = "Login";
            String[] primele = new String[2];
            primele[0] = site;
            primele[1] = current_action;
            String urmatoarele[] = new String[10];
            urmatoarele[0] = "7";
            urmatoarele[1] = "name";
            urmatoarele[2] = name.getText().toString();
            urmatoarele[3] = "email";
            urmatoarele[4] = email.getText().toString();
            urmatoarele[5] = "password";
            urmatoarele[6] = password.getText().toString();
            urmatoarele[7] = "password_confirmation";
            urmatoarele[8] = confirm_password.getText().toString();
            new ATask().execute(primele, urmatoarele);
        }
        else
            Toast.makeText(getApplicationContext(), "The password confirmation does not match!", Toast.LENGTH_LONG).show();
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        Log.e("ceva","A dat cineva click");
        // Result code is RESULT_OK only if the user selects an Image
        if (resultCode == Activity.RESULT_OK)
            switch (requestCode){
                case GALLERY_REQUEST_CODE:
                    //data.getData returns the content URI for the selected Image
                    Uri selectedImage = data.getData();
                    Log.e("ceva", selectedImage.toString());
                    imageView.setImageURI(selectedImage);
                    imageView.setVisibility(View.VISIBLE);
                    //button.setText("Change image");
                    //buttonCreate.setVisibility((View.VISIBLE));
                    //setStylesView();
                    break;
            }
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
                    con.setRequestProperty("Accept",
                            "application/json");

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
                        Log.e("ceva", response.toString());
                        return "The account was created!";

                    }
                    else
                    {
                        Log.e("rasp", "POST request not worked");
                        return "There was a problem signing up! Please check if the data is valid.";

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
        }
    }
}
