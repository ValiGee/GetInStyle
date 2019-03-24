package com.example.getinstyle_login;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.widget.ArrayAdapter;
import android.widget.Spinner;

import java.util.ArrayList;
import java.util.List;

public class SignUpActivity extends AppCompatActivity {

    private Spinner spinnerDay, spinnerMonth, spinnerYear;

    public void addItemsOnSpinnerMonth() {

        spinnerMonth = (Spinner) findViewById(R.id.spinnerMonth);
        List<String> list = new ArrayList<String>();
        list.add("January");
        list.add("February");
        list.add("March");
        list.add("April");
        list.add("May");
        list.add("June");
        list.add("July");
        list.add("August");
        list.add("September");
        list.add("October");
        list.add("November");
        list.add("December");
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(this,
            android.R.layout.simple_spinner_item, list);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerMonth.setAdapter(dataAdapter);
    }

    public void addItemsOnSpinnerDay() {

        spinnerDay = (Spinner) findViewById(R.id.spinnerDay);
        List<String> list = new ArrayList<String>();
        for (int i = 1; i <= 31; i++){
            list.add("" + i);
        }
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(this,
            android.R.layout.simple_spinner_item, list);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerDay.setAdapter(dataAdapter);
    }

    public void addItemsOnSpinnerYaer() {

        spinnerYear = (Spinner) findViewById(R.id.spinnerYear);
        List<String> list = new ArrayList<String>();
        for (int i = 2002; i >= 1940; i--){
            list.add("" + i);
        }
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(this,
            android.R.layout.simple_spinner_item, list);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerYear.setAdapter(dataAdapter);
    }


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_signup);

        addItemsOnSpinnerDay();
        addItemsOnSpinnerMonth();
        addItemsOnSpinnerYaer();
    }
}
