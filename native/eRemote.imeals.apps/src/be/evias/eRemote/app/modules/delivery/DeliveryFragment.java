package be.evias.eRemote.app.modules.delivery;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import be.evias.eRemote.R;
import be.evias.eRemote.lib.AbstractFragment;

public class DeliveryFragment
    extends AbstractFragment
{
    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        View rv = linf.inflate(R.layout.fragment_delivery, container, false);
        return rv;
    }
}