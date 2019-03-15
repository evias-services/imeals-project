package be.evias.eRemote.app.modules.order;

import android.app.FragmentTransaction;
import android.content.DialogInterface;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.*;
import be.evias.eRemote.R;
import be.evias.eRemote.ctrl.MenuController;
import be.evias.eRemote.lib.AbstractFragment;
import be.evias.eRemote.lib.AsyncHTTPTask;
import be.evias.eRemote.lib.SessionManager;

import java.util.ArrayList;

import be.evias.eRemote.app.modules.order.AddCustomizationFragment._Registry;

public class AddMealFragment
    extends AbstractFragment
{
    private MenuController ctrl_menu;
    private AddCustomizationFragment._Registry customization_registry;

    public static final int CTRL_MENU = 0;

    Spinner     category_selector;
    Spinner     meal_selector;
    TextView    quantity_value;
    AddMealFragment _myself;

    AddMealFragment()
    {
        ctrl_menu = new MenuController();
    }

    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        getDialog().setTitle(R.string.addmeal_title);

        /* get view produced by restaurant layout. */
        final View rv = linf.inflate(R.layout.fragment_order_addmeal, container, false);

        /* for each new meal, we get a new customizations
           registry for storing and retrieving customizations. */
        customization_registry  = new AddCustomizationFragment._Registry();

        /* init UI */
        initSelectors(rv);
        initAddCustomization(rv);
        initQuantityWidget(rv);

        /* init fragment close */
        Button close = (Button) rv.findViewById(R.id.process_addmeal);
        close.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                /* onClose save the data to the _Meal registry.*/

                int pos_category  = category_selector.getSelectedItemPosition();
                int pos_meal      = meal_selector.getSelectedItemPosition();
                String category_item  = category_selector.getItemAtPosition(pos_category).toString();
                String meal_item      = meal_selector.getItemAtPosition(pos_meal).toString();
                int    quantity       = Integer.parseInt(quantity_value.getText().toString());

                MenuController ctrl = new MenuController();
                String meal_xml     = ctrl.getMealXML(meal_item);

                /* bind _Meal object */
                MenuController._Meal new_meal = MenuController._Meal.fromXml(meal_xml);
                new_meal.setQuantity(quantity);
                new_meal.setCategory(category_item);
                new_meal.setCustomizations(customization_registry.getList());

                getRegistry().add(new_meal);
                getDialog().dismiss();
            }
        });

        /* return produced layout. */
        return rv;
    }

    private void initSelectors(View parent)
    {
        SessionManager s_mgr    = new SessionManager(getActivity());

        /* generate categories list. */
        String[] categories = ctrl_menu.getCategories(s_mgr.getRestaurantId());
        String[] meals      = ctrl_menu.getMeals(categories[0]);

        /* Fill categories and meals spinners. */
        category_selector = (Spinner) parent.findViewById(R.id.addmeal_spinner_categories);
        ArrayAdapter<String> cadapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item,
                categories);
        cadapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        category_selector.setAdapter(cadapter);
        category_selector.setOnItemSelectedListener(new _CategorySelectionListener());

        meal_selector = (Spinner) parent.findViewById(R.id.addmeal_spinner_meals);
        ArrayAdapter<String> madapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item,
                meals);
        madapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        meal_selector.setAdapter(madapter);
    }

    private void initAddCustomization(View parent)
    {
        Button addc = (Button) parent.findViewById(R.id.addmeal_addcustomization);
        addc.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String meal = meal_selector.getItemAtPosition(meal_selector.getSelectedItemPosition()).toString();
                AddCustomizationFragment subfragment = new AddCustomizationFragment();
                Bundle args = new Bundle();
                args.putString("meal", meal);
                subfragment.setArguments(args);

                /* set registry and close listener to handle
                   the fragments lifecycle correctly. */
                subfragment.setRegistry(customization_registry);
                subfragment.setCloseListener(new _AddCustomizationCloseListener());

                subfragment.show(getChildFragmentManager(), "dialog");
            }
        });
    }

    private void initQuantityWidget(View parent)
    {
        Button q_plus  = (Button) parent.findViewById(R.id.addmeal_quantity_plus);
        Button q_minus = (Button) parent.findViewById(R.id.addmeal_quantity_minus);
        quantity_value = (TextView) parent.findViewById(R.id.addmeal_quantity_value);

        quantity_value.setText("1");

        q_plus.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                String q_val = quantity_value.getText().toString();
                int before = Integer.parseInt(q_val);
                int after = before + 1;

                String new_t = "";
                new_t += after;

                quantity_value.setText(new_t);
            }
        });

        q_minus.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                String q_val = quantity_value.getText().toString();
                int before = Integer.parseInt(q_val);
                int after = before - 1;

                if (after < 1)
                    after = 1;

                String new_t = "";
                new_t += after;

                quantity_value.setText(new_t);
            }
        });
    }

    public void refreshCustomizations()
    {
        TextView customizations_txt = (TextView) getView().findViewById(R.id.addmeal_customizations);
        customizations_txt.setText(customization_registry.toString());
    }

    public class _AddCustomizationCloseListener
        implements DialogInterface.OnDismissListener
    {
        @Override
        public void onDismiss(DialogInterface dialogInterface)
        {
            /* update customizations text list when "add-customization"
               fragment is closed. */
            refreshCustomizations();
        }
    } /* end class AddMealFragment._AddCustomizationCloseListener */

    /**
     * "customizations" are kept in an ArrayList<_Meal>
     * instance as defines AbstractFragment._Registry<>
     */
    public static class _Registry
        extends AbstractFragment._Registry<MenuController._Meal>
    {
        public String toString()
        {
            String out = "";
            for (int i = 0, m = _current.size(); i < m; i++)
                out += _current.get(i).toString() + System.getProperty("line.separator");

            return out;
        }
    } /* end class AddMealFragment._Registry */

    public class _CategorySelectionListener
        implements AdapterView.OnItemSelectedListener
    {
        public String current_category = "";
        public int old_pos = 0;

        public void onItemSelected(AdapterView<?> parent, View view, int pos, long id)
        {
            if (pos == old_pos)
                return ;

            MenuController ctrl = new MenuController();
            current_category    = parent.getItemAtPosition(pos).toString();
            old_pos             = pos;

            /* Update meal_selector with new contents. */
            String[] meals  = ctrl.getMeals(current_category);

            meal_selector.setAdapter(new ArrayAdapter<String>(getActivity(),
                    android.R.layout.simple_spinner_item,
                    meals));
            meal_selector.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> arg0, View arg1, int arg2, long arg3)
                {
                }

                @Override
                public void onNothingSelected(AdapterView<?> arg0)
                {
                }
            });
        }

        public void onNothingSelected(AdapterView parent)
        {
        }
    } /* end class AddMealFragment._CategorySelectionListener */

} /* end class AddMealFragment */