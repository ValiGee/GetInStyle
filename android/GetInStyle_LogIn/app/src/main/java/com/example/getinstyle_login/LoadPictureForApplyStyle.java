package com.example.getinstyle_login;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.provider.MediaStore;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.webkit.MimeTypeMap;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.squareup.picasso.Picasso;

import org.json.JSONObject;

import java.io.File;
import java.io.IOException;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class LoadPictureForApplyStyle extends AppCompatActivity {

    public static final int GALLERY_REQUEST_CODE = 1;
    public int selected = 1; //aici se afla inicele pozei selectate
    public int PICTURE_STYLE_WIDTH = 188;
    public int PICTURE_STYLE_HEIGHT = 250;
    String site_ul;
    String avatar = "";
    MediaType MEDIA_TYPE;
    private int[] styles = {R.drawable.composition_vii,
        R.drawable.la_muse,
        R.drawable.starry_night,
        R.drawable.the_wave,
        R.drawable.candy,
        R.drawable.feathers,
        R.drawable.the_scream,
        R.drawable.mosaic,
        R.drawable.udnie,
        R.drawable.gold_black,
        R.drawable.triangles,
        R.drawable.pink,
        R.drawable.rain,
        R.drawable.landscape,
        R.drawable.flame,
        R.drawable.flame_inversed,
        R.drawable.fire};
    private ImageView imageView;
    private Button button, buttonCreate;
    private LinearLayout linearLayout;

    public static String getRealPathFromUri(Context context, Uri contentUri) {
        Cursor cursor = null;
        try {
            String[] proj = {MediaStore.Images.Media.DATA};
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

    public void selectImage(View view) {
        pickFromGallery();
    }

    public void applyStyle(View view) {
        String site = site_ul + "/api/media/preview";
        String current_action = "Preview";
        String[] primele = new String[2];
        primele[0] = site;
        primele[1] = current_action;
        String[] urmatoarele = new String[10];
        urmatoarele[0] = "2";
        urmatoarele[1] = "style_id";
        urmatoarele[2] = Integer.toString(selected);
        new LoadPictureForApplyStyle.ATask().execute(primele, urmatoarele);
    }

    private void setStylesView() {
        for (int style : styles) {
            final ImageView imageView = new ImageView(this);
            imageView.setLayoutParams(new LinearLayout.LayoutParams(PICTURE_STYLE_WIDTH * 2,
                PICTURE_STYLE_HEIGHT * 2)); // value is in pixels
            imageView.setBackgroundColor(getResources().getColor(R.color.colorAccent));

            imageView.setImageResource(style);
            imageView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    v.setPadding(5, 5, 5, 5);

                    for (int i = 0; i < linearLayout.getChildCount(); i++) {

                        View subView = linearLayout.getChildAt(i);

                        if (subView instanceof ImageView) {
                            ImageView imageView = (ImageView) subView;
                            if (v != imageView)
                                imageView.setPadding(0, 0, 0, 0);
                            else
                                selected = i + 1;
                        }
                    }
//                    System.out.println(selected);
                }
            });
            if (linearLayout != null) {
                linearLayout.addView(imageView);
            }
        }
    }

    private void pickFromGallery() {
        //Create an Intent with action as ACTION_PICK
        Intent intent = new Intent(Intent.ACTION_PICK);
        // Sets the type as image/*. This ensures only components of type image are selected
        intent.setType("image/*");
        //We pass an extra array with the accepted mime types. This will ensure only components with these MIME types as targeted.
        String[] mimeTypes = {"image/jpeg", "image/png"};
        intent.putExtra(Intent.EXTRA_MIME_TYPES, mimeTypes);
        // Launching the Intent
        startActivityForResult(intent, GALLERY_REQUEST_CODE);
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

    @SuppressLint("SetTextI18n")
    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        // Result code is RESULT_OK only if the user selects an Image
        if (resultCode == Activity.RESULT_OK)
            switch (requestCode) {
                case GALLERY_REQUEST_CODE:
                    //data.getData returns the content URI for the selected Image
                    Uri selectedImage = data.getData();
                    imageView.setImageURI(selectedImage);
                    imageView.setVisibility(View.VISIBLE);
                    button.setText("Change image");
                    buttonCreate.setVisibility((View.VISIBLE));
                    avatar = getRealPathFromUri(getApplicationContext(), selectedImage);
                    MEDIA_TYPE = MediaType.parse(getMimeType(selectedImage));
                    setStylesView();
                    break;
            }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_load_picture_for_apply_style);
        site_ul = getApplicationContext().getResources().getString(R.string.site);
        imageView = (ImageView) findViewById(R.id.pictureImageView);
        imageView.setVisibility(View.GONE);
        button = (Button) findViewById((R.id.addImage));
        buttonCreate = (Button) findViewById((R.id.applyStyle));
        buttonCreate.setVisibility((View.GONE));
        linearLayout = (LinearLayout) findViewById(R.id.styles_container);
    }

    public class ATask extends AsyncTask<String[], Void, String> {

        @Override
        protected String doInBackground(String[]... urls) {

            String site = urls[0][0];
            Integer cate = Integer.parseInt(urls[1][0]);
            MultipartBody.Builder builder = new MultipartBody.Builder();

            for (int i = 1; i <= cate; i += 2) {
                String a = urls[1][i];
                String b = urls[1][i + 1];
                Log.e("cheie", a);
                Log.e("valoare", b);
                builder.addFormDataPart(a, b);
            }

            OkHttpClient client = new OkHttpClient();
            RequestBody requestBody;
            if (!avatar.equals("")) {
                Log.e("mime_type", MEDIA_TYPE.toString());
                String[] permissions = {Manifest.permission.WRITE_EXTERNAL_STORAGE, Manifest.permission.READ_EXTERNAL_STORAGE};
                requestPermissions(permissions, 1);
                requestBody = builder
                    .setType(MultipartBody.FORM)
                    .addFormDataPart("userPhoto", avatar,
                        RequestBody.create(MEDIA_TYPE, new File(avatar)))
                    .build();
            } else {
                requestBody = builder
                    .setType(MultipartBody.FORM)
                    .build();
            }

            Request request = new Request.Builder()
                .header("Accept", "application/json")
                .url(site)
                .post(requestBody)
                .build();

            try (Response response = client.newCall(request).execute()) {
                if (!response.isSuccessful()) throw new IOException("Unexpected code " + response);
                String raspuns = response.body().string();
                Log.e("a mers", raspuns);
                return raspuns;
            } catch (Exception e) {
                Log.e("eroare", e.getMessage());
                return "Invalid data!";
            }

        }

        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            try {
                JSONObject ceva = new JSONObject(result);
                Log.e("URL", site_ul + "/" + ceva.getString("stylized_path"));
                Picasso.get().load(site_ul + "/" + ceva.getString("stylized_path")).into(imageView);
                Toast.makeText(getApplicationContext(), "If you want to save the picture you must log in first!", Toast.LENGTH_LONG).show();
            } catch (Throwable t) {
                Log.e("Eroare JSON", t.getMessage());
                Toast.makeText(getApplicationContext(), "An unknown error has occurred!", Toast.LENGTH_LONG).show();
            }
        }
    }
}
