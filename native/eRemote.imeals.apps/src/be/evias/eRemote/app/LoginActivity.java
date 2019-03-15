package be.evias.eRemote.app;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.*;
import be.evias.eRemote.R;
import be.evias.eRemote.ctrl.AuthController;
import be.evias.eRemote.ctrl.RestaurantController;
import be.evias.eRemote.lib.*;

public class LoginActivity
    extends AbstractActivity
{
    private SessionManager session_mgr;
    private EditText t_account;
    private EditText t_cred;
    private Spinner  t_rest;

    private AlertDialogManager alert_mgr = new AlertDialogManager();

    public class restaurantSelectedListener
        implements AdapterView.OnItemSelectedListener
    {
        public String current_restaurant = "";

        public void onItemSelected(AdapterView<?> parent, View view, int pos, long id)
        {
            current_restaurant = parent.getItemAtPosition(pos).toString();
        }

        public void onNothingSelected(AdapterView parent)
        {
            // Do nothing.
        }

        public restaurantSelectedListener()
        {
            current_restaurant = "eRestaurant (La Calamine)";
        }

        public String getSelectedText()
        {
            return current_restaurant;
        }
    }

    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login);

        /* generate restaurant list. */
        RestaurantController ctrl = new RestaurantController();
        String[] restaurants = ctrl.getRestaurants();

        /* Fill restaurant spinner. */
        Spinner s = (Spinner) findViewById(R.id.select_restaurant);
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(getApplicationContext(),
                android.R.layout.simple_spinner_item,
                restaurants);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        s.setAdapter(adapter);

        session_mgr = new SessionManager(getApplicationContext());

        t_account      = (EditText) findViewById(R.id.login_account);
        t_cred         = (EditText) findViewById(R.id.login_cred);
        t_rest         = (Spinner) findViewById(R.id.select_restaurant);
        t_rest.setOnItemSelectedListener(new restaurantSelectedListener());

        Button b_login = (Button) findViewById(R.id.login_process);
        b_login.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0)
            {
                String uname  = t_account.getText().toString();
                String ucred  = t_cred.getText().toString();
                String rest   = ((restaurantSelectedListener) t_rest.getOnItemSelectedListener()).getSelectedText();

                if ( ! (uname.trim().length() > 0 && ucred.trim().length() > 0)) {
                    alert_mgr.showAlertDialog(LoginActivity.this, "Authentication error", "Please enter username and password", false);
                    return ;
                }

                /* process username/password/restaurant login */
                processForm(uname, ucred, rest);
            }
        });
    }

    private boolean processForm(String uname, String ucred, String restaurant)
    {
        /* check authentication using AuthController and check
           result on AuthController._Identity instance. */
        AuthController ctrl = new AuthController();
        AuthController._Identity identity;

        identity = ctrl.checkAuthentication(uname, ucred);
        if (identity.isValid()) {

            /* update current Android session. */
            session_mgr.createLoginSession(
                    identity.getRealname(),
                    identity.getEmail(),
                    identity.getLogin(),
                    identity.getId());
            session_mgr.setRestaurant(restaurant);

            /* start main activity. */
            Intent i = new Intent(getApplicationContext(), MainActivity.class);
            startActivity(i);

            /* finish LoginActivity */
            finish();
            return true;
        }
        else {
            /* Wrong username / password */
            alert_mgr.showAlertDialog(LoginActivity.this, "Authentication error", "Username / Password combination is invalid.", false);
            return false;
        }
    }

} /* end class LoginActivity */