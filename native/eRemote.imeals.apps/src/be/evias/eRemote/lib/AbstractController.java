package be.evias.eRemote.lib;

import java.util.concurrent.ExecutionException;

public class AbstractController
{
    public String executeHTTPRequest(String url)
    {
        String response = "";
        try {
            response = new AsyncHTTPTask().execute(url).get();
        }
        catch (InterruptedException e) {
            e.printStackTrace();
        }
        catch (ExecutionException e) {
            e.printStackTrace();
        }

        return response;
    }
}