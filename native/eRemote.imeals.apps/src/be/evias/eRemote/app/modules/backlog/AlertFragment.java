package be.evias.eRemote.app.modules.backlog;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import be.evias.eRemote.R;
import be.evias.eRemote.lib.AbstractFragment;

public class AlertFragment
    extends AbstractFragment
{
    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        if (container == null)
            return null;

        /* get view produced by layout. */
        return linf.inflate(R.layout.fragment_alert, container, false);
    }
}