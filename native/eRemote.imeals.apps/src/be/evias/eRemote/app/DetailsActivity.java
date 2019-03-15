package be.evias.eRemote.app;

import android.app.Activity;
import android.content.res.Configuration;
import android.os.Bundle;
import be.evias.eRemote.lib.AbstractFragment;
import be.evias.eRemote.lib.FragmentFactory;
import be.evias.eRemote.lib.SessionManager;

public class DetailsActivity
    extends Activity
{
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);

        if (getResources().getConfiguration().orientation
                == Configuration.ORIENTATION_LANDSCAPE) {
            /* No need for this activity in landscape mode.
               Details fragment are displayed directly in
               MenuFragment. */
            finish();
            return;
        }

        if (savedInstanceState == null) {
            /* Initialize details fragments container. */
            int index = getIntent().getIntExtra("index", 0);

            SessionManager session_mgr = new SessionManager(getApplicationContext());

            if (index == FragmentFactory.FRAGMENT_LOGOUT) {
                /* logout current user. */
                session_mgr.logoutUser();
                finish();
                return;
            }
            else {
                /* display selected fragment. */
                AbstractFragment fragment = FragmentFactory.getFragment(index);

                getFragmentManager().beginTransaction()
                    .add(android.R.id.content, fragment)
                    .commit();
            }
        }
    }
}