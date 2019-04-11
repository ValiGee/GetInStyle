package com.example.getinstyle_login;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.provider.MediaStore;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.os.AsyncTask;
import android.util.Log;
import android.webkit.MimeTypeMap;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Toast;
import java.io.File;
import java.io.IOException;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class SignUpActivity extends AppCompatActivity {

    public void testButtonOnClick(View view){
        startActivity(new Intent(SignUpActivity.this, PhotoPage.class));
    }

    EditText email, name, password, confirm_password;
    private ImageView imageView;
    public static final int GALLERY_REQUEST_CODE = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_signup);
        site_ul = getApplicationContext().getResources().getString(R.string.site);
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

    public static String getRealPathFromUri(Context context, Uri contentUri) {
        Cursor cursor = null;
        try {
            String[] proj = { MediaStore.Images.Media.DATA };
            cursor = context.getContentResolver().query(contentUri, proj, null, null, null);
            int column_index = cursor.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);
            cursor.moveToFirst();
            return cursor.getString(column_index);
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
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

    String site_ul;
    String avatar = "";
    MediaType MEDIA_TYPE;

    public void createAccountOnClick(View view)
    {
        if(password.getText().toString().equals(confirm_password.getText().toString())) {
            String site = site_ul + "/api/register";
            String current_action = "Register";
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
        // Result code is RESULT_OK only if the user selects an Image
        if (resultCode == Activity.RESULT_OK)
            switch (requestCode){
                case GALLERY_REQUEST_CODE:
                    //data.getData returns the content URI for the selected Image
                    Uri selectedImage = data.getData();
                    Log.e("uri_imagine", selectedImage.toString());
                    imageView.setImageURI(selectedImage);
                    imageView.setVisibility(View.VISIBLE);
                    avatar = getRealPathFromUri(getApplicationContext(), selectedImage);
                    MEDIA_TYPE = MediaType.parse(getMimeType(selectedImage));
                    break;
            }
    }

    public String getMimeType(Uri uri) {
        String mimeType = null;
        if (uri.getScheme().equals(ContentResolver.SCHEME_CONTENT)) {
            ContentResolver cr = getApplicationContext().getContentResolver();
            mimeType = cr.getType(uri);
        } else {
            String fileExtension = MimeTypeMap.getFileExtensionFromUrl(uri
                    .toString());
            mimeType = MimeTypeMap.getSingleton().getMimeTypeFromExtension(
                    fileExtension.toLowerCase());
        }
        return mimeType;
    }

    public class ATask extends AsyncTask<String[], Void, String> {

        @Override
        protected String doInBackground(String[]... urls) {

                String site = urls[0][0];
                Integer cate = Integer.parseInt(urls[1][0]);
                MultipartBody.Builder builder = new MultipartBody.Builder();

                for(int i = 1; i <= cate; i += 2)
                {
                    String a = urls[1][i];
                    String b = urls[1][i + 1];
                    Log.e("cheie", a);
                    Log.e("valoare", b);
                    builder.addFormDataPart(a, b);
                }

                OkHttpClient client = new OkHttpClient();
                RequestBody requestBody;
                if(!avatar.equals(""))
                {
                    Log.e("mime_type", MEDIA_TYPE.toString());
                    String[] permissions = {Manifest.permission.WRITE_EXTERNAL_STORAGE,Manifest.permission.READ_EXTERNAL_STORAGE};
                    requestPermissions(permissions,1);
                    requestBody = builder
                            .setType(MultipartBody.FORM)
                            .addFormDataPart("avatar", avatar,
                                    RequestBody.create(MEDIA_TYPE, new File(avatar)))
                            .build();
                }
                else
                {
                    requestBody = builder
                            .setType(MultipartBody.FORM)
                            .build();
                }

                Request request = new Request.Builder()
                        .header("Accept", "application/json")
                        .url(site)
                        .post(requestBody)
                        .build();

                try (Response response = client.newCall(request).execute())
                {
                    if (!response.isSuccessful()) throw new IOException("Unexpected code " + response);

                    Log.e("a mers", response.body().string());
                    return "Account created!";
                }
                catch(Exception e)
                {
                    Log.e("eroare", e.getMessage());
                    return "Invalid data!";
                }

        }

        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            Toast.makeText(getApplicationContext(), result, Toast.LENGTH_LONG).show();
        }
    }
}
