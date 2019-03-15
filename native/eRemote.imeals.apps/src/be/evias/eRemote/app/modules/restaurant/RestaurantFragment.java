package be.evias.eRemote.app.modules.restaurant;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import be.evias.eRemote.R;
import be.evias.eRemote.lib.AbstractFragment;
import be.evias.eRemote.lib.SecureData;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class RestaurantFragment
    extends AbstractFragment
{
    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        if (container == null)
            return null;

        /* get view produced by restaurant layout. */
        View rv = linf.inflate(R.layout.fragment_restaurant, container, false);

        /* return produced layout. */
        return rv;
    }
}