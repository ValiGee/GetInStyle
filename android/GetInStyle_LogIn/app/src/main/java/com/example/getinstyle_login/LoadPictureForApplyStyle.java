package com.example.getinstyle_login;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.support.annotation.ColorInt;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Toast;

import java.lang.reflect.Array;

public class LoadPictureForApplyStyle extends AppCompatActivity {

    private int[] styles = { R.drawable.candy,
                             R.drawable.composition_vii,
                             R.drawable.la_muse,
                             R.drawable.mosaic,
                             R.drawable.starry_night_crop,
                             R.drawable.the_scream,
                             R.drawable.udnie,
                             R.drawable.wave_crop};
    private ImageView imageView;
    private Button button, buttonCreate;
    private LinearLayout linearLayout;
    public static final int GALLERY_REQUEST_CODE = 1;

    public void selectImage(View view){
        pickFromGallery();
    }

    public void applyStyle(View view){
//        TO DO:
    }

    private void setStylesView() {
        for (int style : styles) {
            final ImageView imageView = new ImageView(this);
            imageView.setLayoutParams(new LinearLayout.LayoutParams(400, 400)); // value is in pixels
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
                            if(v != imageView)
                                imageView.setPadding(0, 0, 0, 0);
                        }
                    }
                }
            });
            if (linearLayout != null) {
                linearLayout.addView(imageView);
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
                    setStylesView();
                    break;
            }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_load_picture_for_apply_style);
        imageView = (ImageView) findViewById(R.id.pictureImageView);
        imageView.setVisibility(View.GONE);
        button = (Button) findViewById((R.id.addImage));
        buttonCreate = (Button) findViewById((R.id.applyStyle));
        buttonCreate.setVisibility((View.GONE));
        linearLayout = (LinearLayout) findViewById(R.id.styles_container);
    }
}
