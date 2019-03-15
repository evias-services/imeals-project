package be.evias.eRemote.lib;

import android.util.Log;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import android.os.AsyncTask;
import org.apache.http.util.EntityUtils;

import java.io.IOException;
import java.io.InputStream;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Package: be.evias.eRemote
 * Project: eRemote
 * User: greg
 * Date: 29.07.13
 * Time: 14:50
 *
 * Implementation of class AsyncHTTPTask for performing
 * asynchronous HTTP requests with the Android API.
 */
public class AsyncHTTPTask
    extends AsyncTask<String, Void, String>
{
    public enum _Method {
        GET, POST
    }

    private Exception exception;
    private boolean trim;
    private _Method method;

    AsyncHTTPTask()
    {
        trim   = true;
        method = _Method.GET;
    }

    public void setTrim(boolean t)
    {
        trim = t;
    }

    public boolean getTrim()
    {
        return trim;
    }

    public void setMethod(_Method m)
    {
        method = m;
    }

    public _Method getMethod()
    {
        return method;
    }

    protected String doInBackground(String... uris)
    {
        for (int i = 0, m = uris.length; i < m; i++) {

            String request_uri = "http://services.e-restaurant.evias.loc";

            /* XXX regular expression validation. */
            request_uri += uris[i];

            HttpClient client = new DefaultHttpClient();

            try {
                HttpGet  req_get;
                HttpPost req_post;
                HttpResponse response;
                switch (getMethod()) {
                    default:
                    case GET:
                        req_get  = new HttpGet(request_uri);
                        response = client.execute(req_get);
                        break;

                    case POST:
                        req_post = new HttpPost(request_uri);

                        /* XXX Build parameters */

                        response = client.execute(req_post);
                        break;
                }

                String res = EntityUtils.toString(response.getEntity());

                if (getTrim())
                    res = res.replaceAll("(\\r|\\n)", "");

                Log.d("AsyncHTTPTask/doInBackground", "'" + request_uri + "' RESPONSE: '" + res + "'");

                return res;
            }
            catch (ClientProtocolException e) {
                Log.e("AsyncHTTPTask/doInBackground@CPException", e.getMessage());
            }
            catch (IOException e) {
                Log.e("AsyncHTTPTask/doInBackground@IOException", e.getMessage());
            }
        }

        return "";
    }

    protected void onPostExecute(String res)
    {
        Log.d("AsyncHTTPTask/onPostExecute", "Response: '" + res + "'");
    }

    public static String extractFromXml(String extract, String input)
    {
        /* match pattern without boundaries */
        Pattern pattern = Pattern.compile(".*" + extract + ".*");
        Matcher matcher = pattern.matcher(input);

        if (! matcher.matches())
            return "";

        return matcher.group(1);
    }
}
