package be.evias.eRemote.lib;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.util.Log;
import be.evias.eRemote.app.LoginActivity;
import be.evias.eRemote.ctrl.RestaurantController;

import java.util.HashMap;

public class SessionManager
{
    SharedPreferences pref;
    SharedPreferences.Editor editor;
    Context _context;
    int PRIVATE_MODE = 0;

    private static final String PREF_NAME = "eVias_eRemotePref";
    private static final String IS_LOGIN = "IsLoggedIn";

    public static final String KEY_NAME  = "name";
    public static final String KEY_EMAIL = "email";
    public static final String KEY_ID    = "id";
    public static final String KEY_LOGIN = "login";
    public static final String KEY_RESTAURANT   = "restaurant";
    public static final String KEY_RESTAURANTID = "id_restaurant";

    public SessionManager(Context context)
    {
        this._context = context;
        pref = _context.getSharedPreferences(PREF_NAME, PRIVATE_MODE);
        editor = pref.edit();
    }

    public void createLoginSession(String name, String email, String login, String id)
    {
        editor.putBoolean(IS_LOGIN, true);
        editor.putString(KEY_NAME, name);
        editor.putString(KEY_EMAIL, email);
        editor.putString(KEY_LOGIN, login);
        editor.putString(KEY_ID, id);
        editor.commit();
    }

    public void setRestaurant(String restaurant)
    {
        /* XXX refactor, dependency injection HERE. */
        RestaurantController ctrl = new RestaurantController();

        int rid    = 1;

        /* get restaurant data from title. */
        String xml = ctrl.getRestaurantXML(restaurant);
        String id  = AsyncHTTPTask.extractFromXml("<id_restaurant>([0-9]*)</id_restaurant>", xml);

        rid = Integer.parseInt(id);

        editor.putInt(KEY_RESTAURANTID, rid);
        editor.putString(KEY_RESTAURANT, restaurant);
        editor.commit();

        Log.d("SessionManager", "current restaurant: " + rid + " '" + restaurant + "'");
    }

    public String getRestaurant()
    {
        return pref.getString(KEY_RESTAURANT, "eRestaurant (La Calamine)");
    }

    public int getRestaurantId()
    {
        return pref.getInt(KEY_RESTAURANTID, 1);
    }

    public void checkLogin()
    {
        if (! this.isLoggedIn()) {
            Intent i = new Intent(_context, LoginActivity.class);
            i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            _context.startActivity(i);
        }

    }

    public HashMap<String, String> getUserDetails()
    {
        HashMap<String, String> user = new HashMap<String, String>();
        user.put(KEY_NAME, pref.getString(KEY_NAME, null));
        user.put(KEY_EMAIL, pref.getString(KEY_EMAIL, null));
        user.put(KEY_LOGIN, pref.getString(KEY_LOGIN, null));
        user.put(KEY_ID, pref.getString(KEY_ID, null));
        return user;
    }

    public void logoutUser()
    {
        editor.clear();
        editor.commit();

        Intent i = new Intent(_context, LoginActivity.class);
        i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        _context.startActivity(i);
    }

    public boolean isLoggedIn()
    {
        return pref.getBoolean(IS_LOGIN, false);
    }
}