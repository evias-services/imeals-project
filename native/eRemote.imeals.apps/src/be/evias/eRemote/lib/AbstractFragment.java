package be.evias.eRemote.lib;

import android.app.DialogFragment;
import android.content.DialogInterface;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import java.util.ArrayList;
import java.util.concurrent.ExecutionException;

/*
 * Choice of extending DialogFragment is for
 * embedding(-or not) possibility.
 */
public class AbstractFragment
    extends DialogFragment
{
    private DialogInterface.OnDismissListener close_listener;
    private AbstractFragment._Registry registry;

    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        if (container == null)
            return null;

        return super.onCreateView(linf, container, sis);
    }

    public int getShownIndex()
    {
        return getArguments().getInt("index", 0);
    }

    public void setCloseListener(DialogInterface.OnDismissListener l)
    {
        close_listener = l;
    }

    public DialogInterface.OnDismissListener getCloseListener()
    {
        return close_listener;
    }

    public void setRegistry(AbstractFragment._Registry reg)
    {
        registry = reg;
    }

    public AbstractFragment._Registry getRegistry()
    {
        return registry;
    }

    @Override
    public void onDismiss(DialogInterface dialog)
    {
        if (null != getCloseListener())
            /* propagate dismiss event to registered close listener */
            getCloseListener().onDismiss(getDialog());
    }

    abstract public static class _Registry<__ItemClass>
    {
        protected ArrayList<__ItemClass> _current = new ArrayList<__ItemClass>();

        abstract public String toString();

        public int size()
        {
            return _current.size();
        }

        public void add(__ItemClass i)
        {
            _current.add(i);
        }

        public ArrayList<__ItemClass> getList()
        {
            return _current;
        }
    }
}