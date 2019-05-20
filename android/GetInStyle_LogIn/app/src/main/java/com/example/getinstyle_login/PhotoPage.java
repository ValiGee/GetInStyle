package com.example.getinstyle_login;

import android.graphics.Typeface;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import java.lang.reflect.Array;
import java.util.Arrays;
import java.util.List;

public class PhotoPage extends AppCompatActivity {

    private LinearLayout linearLayout;

    public static void setMargins (View v, int left, int top, int right, int bottom) {
        if (v.getLayoutParams() instanceof ViewGroup.MarginLayoutParams) {
            ViewGroup.MarginLayoutParams p = (ViewGroup.MarginLayoutParams) v.getLayoutParams();
            p.setMargins(left, top, right, bottom);
            v.requestLayout();
        }
    }

    public void setTags(List<String> tags) {
        for(String tag : tags) {
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

        linearLayout = (LinearLayout) findViewById(R.id.tags_container);

        setTags(Arrays.asList("#style", "#colors", "#ola", "cicos")); // TO DO
    }
}
