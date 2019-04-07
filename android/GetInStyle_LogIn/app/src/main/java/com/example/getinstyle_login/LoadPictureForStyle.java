package com.example.getinstyle_login;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.provider.MediaStore;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ImageView;

public class LoadPictureForStyle extends AppCompatActivity {

    private ImageView imageView;
    private Button button, buttonCreate;
    public static final int GALLERY_REQUEST_CODE = 1;

    public void selectImage(View view){
        pickFromGallery();
    }

    public void createStyle(View view){
//        TO DO: Back to home page
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
                    imageView.setImageURI(selectedImage);
                    imageView.setVisibility(View.VISIBLE);
                    button.setText("Change image");
                    buttonCreate.setVisibility((View.VISIBLE));
                    break;
            }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_load_picture_for_style);
        imageView = (ImageView) findViewById(R.id.styleImageView);
        imageView.setVisibility(View.GONE);
        button = (Button) findViewById((R.id.addImage));
        buttonCreate = (Button) findViewById((R.id.createStyle));
        buttonCreate.setVisibility((View.GONE));
    }
}
