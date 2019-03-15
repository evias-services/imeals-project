package be.evias.eRemote.app.modules.order;

import android.content.DialogInterface;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import be.evias.eRemote.R;
import be.evias.eRemote.ctrl.MenuController;
import be.evias.eRemote.lib.AbstractFragment;

import java.util.ArrayList;

public class AddCustomizationFragment
    extends AbstractFragment
{
    private Spinner customizations_selector;
    private Spinner actions_selector;

    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        getDialog().setTitle(R.string.addcustomization_title);
        getDialog().setCancelable(false);

        View rv = linf.inflate(R.layout.fragment_order_addcustomization, container, false);

        Bundle args = getArguments();
        MenuController ctrl     = new MenuController();
        String[] customizations = ctrl.getCustomizations(args.getString("meal"));

        customizations_selector = (Spinner) rv.findViewById(R.id.addcustomization_spinner_customizations);
        ArrayAdapter<String> cuadapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item,
                customizations);
        cuadapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        customizations_selector.setAdapter(cuadapter);

        actions_selector = (Spinner) rv.findViewById(R.id.addcustomization_spinner_actions);
        ArrayAdapter<CharSequence> aadapter = ArrayAdapter.createFromResource(getActivity(),
                R.array.customization_actions,
                android.R.layout.simple_spinner_item);
        aadapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        actions_selector.setAdapter(aadapter);

        Button close = (Button) rv.findViewById(R.id.addcustomization_process);
        close.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                int pos_custom      = customizations_selector.getSelectedItemPosition();
                int pos_action      = actions_selector.getSelectedItemPosition();
                String custom_item  = customizations_selector.getItemAtPosition(pos_custom).toString();
                String action       = actions_selector.getItemAtPosition(pos_action).toString();

                action = action.substring(0, 1);

                if (action.compareTo("-") == 0)
                    /* remove price */
                    custom_item = custom_item.substring(0, custom_item.indexOf("(") - 2);

                String new_customization = " " + action + " " + custom_item;

                getRegistry().add(new_customization);
                getDialog().dismiss();
            }
        });

        return rv;
    }

    /**
     * "customizations" are kept in an ArrayList<String>
     * instance as defines AbstractFragment._Registry<>
     */
    public static class _Registry
        extends AbstractFragment._Registry<String>
    {
        public String toString()
        {
            String out = "";
            for (int i = 0, m = _current.size(); i < m; i++)
                out += _current.get(i) + System.getProperty("line.separator");

            return out;
        }
    }
}