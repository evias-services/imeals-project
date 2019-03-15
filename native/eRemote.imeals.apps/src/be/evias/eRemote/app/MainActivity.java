package be.evias.eRemote.app;

/* Android packages dependencies */
import android.app.Activity;
import android.os.Bundle;
import be.evias.eRemote.R;
import be.evias.eRemote.lib.SessionManager;

/**
 * Package: be.evias.eRemote
 * Project: eRemote
 * User: greg
 * Date: 29.07.13
 * Time: 19:11
 *
 * Implementation of Activity class MainActivity.
 * This activity defines the main activity of the
 * eRemote application.
 */
public class MainActivity
    extends Activity
{
    SessionManager session_mgr;

    /**
     * Called when the activity is first created.
     *
     * @param savedInstanceState    Bundle  Last saved State of the application.
     */
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);

        /* Check user session presence or redirect. */
        session_mgr = new SessionManager(getApplicationContext());
        session_mgr.checkLogin();
    }
}
