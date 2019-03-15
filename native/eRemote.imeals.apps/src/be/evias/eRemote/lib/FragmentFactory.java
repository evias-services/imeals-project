package be.evias.eRemote.lib;

import android.os.Bundle;
import be.evias.eRemote.app.modules.backlog.AlertFragment;
import be.evias.eRemote.app.modules.delivery.DeliveryFragment;
import be.evias.eRemote.app.modules.order.OrderFragment;
import be.evias.eRemote.app.modules.restaurant.RestaurantFragment;
import be.evias.eRemote.app.modules.settings.PreferenceFragment;

final public class FragmentFactory
{
    public static final int FRAGMENT_RESTAURANT = 0;
    public static final int FRAGMENT_ORDER      = 1;
    public static final int FRAGMENT_ALERT      = 2;
    public static final int FRAGMENT_DELIVERY   = 3;
    public static final int FRAGMENT_PREFERENCE = 4;
    public static final int FRAGMENT_LOGOUT     = 5;

    public static AbstractFragment getFragment(int which)
    {
        AbstractFragment fragment;

        /* Get corresponding instantiation. */
        switch (which) {
            case FragmentFactory.FRAGMENT_RESTAURANT:
                fragment = new RestaurantFragment();
                break;

            case FragmentFactory.FRAGMENT_ORDER:
                fragment = new OrderFragment();
                break;

            case FragmentFactory.FRAGMENT_ALERT:
                fragment = new AlertFragment();
                break;

            case FragmentFactory.FRAGMENT_DELIVERY:
                fragment = new DeliveryFragment();
                break;

            case FragmentFactory.FRAGMENT_PREFERENCE:
                fragment = new PreferenceFragment();
                break;

            default:
                throw new RuntimeException("wrong fragment id");
        }

        /* Configure fragment arguments. */
        Bundle args = new Bundle();
        args.putInt("index", which);

        fragment.setArguments(args);
        return fragment;
    }
}