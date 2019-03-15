package be.evias.eRemote.app.modules.order;

import android.content.DialogInterface;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.*;
import be.evias.eRemote.R;
import be.evias.eRemote.ctrl.RestaurantController;
import be.evias.eRemote.lib.AbstractFragment;
import be.evias.eRemote.lib.SessionManager;

public class OrderFragment
    extends AbstractFragment
{
    private AddMealFragment._Registry meal_registry;

    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        if (container == null)
            return null;

        View rv = linf.inflate(R.layout.fragment_order, container, false);

        SessionManager s_mgr = new SessionManager(getActivity());
        meal_registry = new AddMealFragment._Registry();

        /* generate tables list. */
        RestaurantController ctrl = new RestaurantController();
        String[] tables = ctrl.getTables(s_mgr.getRestaurantId());

        /* Fill tables spinner. */
        Spinner s = (Spinner) rv.findViewById(R.id.tables_spinner);
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item,
                tables);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        s.setAdapter(adapter);

        Button am = (Button) rv.findViewById(R.id.add_meal);
        am.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                AddMealFragment fragment = new AddMealFragment();

                /* set registry and close listener to handle
                   the fragments lifecycle correctly. */
                fragment.setCloseListener(new _AddMealCloseListener());
                fragment.setRegistry(meal_registry);

                fragment.show(getFragmentManager(), "dialog");
            }
        });

        /* get view from layout */
        return rv;
    }

    public void refreshMeals()
    {
        TextView meals_txt = (TextView) getView().findViewById(R.id.order_meals_list);
        meals_txt.setText(meal_registry.toString());
    }

    public class _AddMealCloseListener
            implements DialogInterface.OnDismissListener
    {
        @Override
        public void onDismiss(DialogInterface dialogInterface)
        {
            /* update customizations text list when "add-customization"
               fragment is closed. */
            refreshMeals();
        }
    }

}