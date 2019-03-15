package be.evias.eRemote.app;

import android.app.FragmentTransaction;
import android.app.ListFragment;
import android.content.Intent;
import android.content.res.Resources;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import be.evias.eRemote.R;
import be.evias.eRemote.lib.AbstractFragment;
import be.evias.eRemote.lib.FragmentFactory;

public class MenuFragment
    extends ListFragment
{
    /**
     * @var m_isDual boolean
     *
     * Wether we are in dual-view mode.
     * Dual view mode means the ListFragment
     * and AbstractFragment are both visible.
     **/
    boolean m_isDual;

    /**
     * @var m_curSelection integer
     * @see be.evias.eRemote.lib.FragmentFactory.FRAGMENT_* constants
     *
     * Represents the index of the current
     * selected action.
     */
    int m_curSelection = 0;

    @Override
    public void onActivityCreated(Bundle sis)
    {
        super.onActivityCreated(sis);

        /* Configure fragment list adapter. */
        Resources res    = getResources();
        String[] actions = res.getStringArray(R.array.array_action_list);
        setListAdapter(new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_list_item_activated_1,
                actions));

        /* Define m_isDual, only if a imeals_logo_details view is available
           means we are in dual mode. */
        View detailsFrame = getActivity().findViewById(R.id.details_fragment);
        m_isDual = detailsFrame != null
                    && detailsFrame.getVisibility() == View.VISIBLE
                    && detailsFrame.findViewById(R.id.imeals_logo_details) != null;

        /* Selection from savedInstanceState */
        if (sis != null)
            // Restore last state for checked position.
            m_curSelection = sis.getInt("curChoice", 0);

        if (m_isDual) {
            /* Highlight selected item and show details fragment */

            getListView().setChoiceMode(ListView.CHOICE_MODE_SINGLE);
            showDetails(m_curSelection);
        }
    }

    @Override
    public View onCreateView(LayoutInflater linf, ViewGroup container, Bundle sis)
    {
        View v = super.onCreateView(linf, container, sis);
        return v;
    }

    @Override
    public void onSaveInstanceState(Bundle outState) {
        super.onSaveInstanceState(outState);

        /* Save current selected action */
        outState.putInt("curChoice", m_curSelection);
    }

    @Override
    public void onListItemClick(ListView l, View v, int position, long id)
    {
        /* Display details fragment */
        showDetails(position);
    }

    void showDetails(int index)
    {
        m_curSelection = index;

        if (m_isDual) {
            /* Display AbstractFragment corresponding to clicked
               index. (@see FragmentFactory.FRAGMENT_*)
               The fragment is displayed as such and is display
               on the right-side of the MenuFragment. */
            getListView().setItemChecked(index, true);

            AbstractFragment details = (AbstractFragment) getFragmentManager()
                    .findFragmentById(R.id.details_fragment);
            if (details == null || details.getShownIndex() != m_curSelection) {
                /* Fragment update needed */
                details = FragmentFactory.getFragment(m_curSelection);

                FragmentTransaction ft = getFragmentManager().beginTransaction();
                if (index == 0)
                    ft.replace(R.id.details_fragment, details);
                else
                    ft.replace(R.id.menu_fragment, details);

                ft.setTransition(FragmentTransaction.TRANSIT_FRAGMENT_FADE);
                ft.commit();
            }

        }
        else {
            /* Launch new activity containing correspondind details fragment. */
            Intent intent = new Intent();
            intent.setClass(getActivity(), DetailsActivity.class);
            intent.putExtra("index", index);
            startActivity(intent);
        }
    }
}