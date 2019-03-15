package be.evias.eRemote.ctrl;

import be.evias.eRemote.lib.AbstractController;
import be.evias.eRemote.lib.AsyncHTTPTask;
import be.evias.eRemote.lib.SecureData;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;


public class RestaurantController
        extends AbstractController
{
    public String[] getRestaurants()
    {
        String[] restaurants;
        String get_uri = "/restaurant/index/format/xml";

        /* get restaurants list. */
        String response = executeHTTPRequest(get_uri);

        List<String> matches = new ArrayList<String>();
        Pattern pattern = Pattern.compile("(<title>)(.*?)(</title>)");
        Matcher matcher = pattern.matcher(response);

        while (matcher.find())
            matches.add(matcher.group(2));

        restaurants = matches.toArray(new String[0]);
        return restaurants;
    }

    public String[] getTables(int restaurant_id)
    {
        String[] tables;
        String get_uri = "/room-table/index/restaurant_id/" + restaurant_id + "/format/xml";

        /* get restaurants list. */
        String response = executeHTTPRequest(get_uri);

        List<String> matches = new ArrayList<String>();
        String room_part  = "(<id_room>)(.*?)(</id_room>)";
        String table_part = "(<table_number>)(.*?)(</table_number>)";
        Pattern pattern = Pattern.compile("(<item>)(.*?)" + room_part + "(.*?)" + table_part + "(.*?)(</item>)");
        Matcher matcher = pattern.matcher(response);

        while (matcher.find())
            matches.add("Salle " + matcher.group(4) + " : Table " + matcher.group(8));

        tables = matches.toArray(new String[0]);
        return tables;
    }

    public String getRestaurantXML(String title)
    {
        String get_uri = "/restaurant/get/title/";
        get_uri += SecureData.getInstance().encrypt(title);
        get_uri += "/format/xml";

        String response = executeHTTPRequest(get_uri);
        return response;
    }
}