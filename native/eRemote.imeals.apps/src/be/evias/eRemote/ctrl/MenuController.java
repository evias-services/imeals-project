package be.evias.eRemote.ctrl;

import be.evias.eRemote.lib.AbstractController;
import be.evias.eRemote.lib.AsyncHTTPTask;
import be.evias.eRemote.lib.SecureData;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class MenuController
    extends AbstractController
{
    /**
     * XXX
     *
     * XXX add price    
     */
    public static class _Meal
    {
        String meal_menu_number;
        String category_label;
        String meal_label;
        boolean customizable;
        int    quantity;
        ArrayList<String> customizations;

        public _Meal()
        {
            meal_menu_number = "";
            category_label = "";
            meal_label     = "";
            customizable   = false;
            quantity       = 1;
            customizations = new ArrayList<String>();
        }

        public int getQuantity() { return quantity; }
        public String getCategory() { return category_label; }
        public String getMeal() { return meal_label; }
        public String getMealNumber() { return meal_menu_number; }
        public boolean isCustomizable() { return customizable; }
        public ArrayList<String> getCustomizations() { return customizations; }

        public void setQuantity(int q) { quantity = q; }
        public void setCategory(String c) { category_label = c; }
        public void setMeal(String m) { meal_label = m; }
        public void setMealNumber(String m) { meal_menu_number = m; }
        public void setCustomizable(boolean c) { customizable = c; }
        public void setCustomizations(ArrayList<String> c) { customizations = c; }

        public String toString()
        {
            String out = quantity + "x #" + meal_menu_number
                    + " " + category_label
                    + ": " + meal_label;

            for (int i = 0, m = customizations.size(); i < m; i++)
                out += System.getProperty("line.separator") + customizations.get(i);

            return out;
        }

        public static _Meal fromXml(String xml)
        {
            String title_extract   = "<title>([\\w -_/]*)</title>";
            String mealnum_extract = "<meal_menu_number>([0-9]*)</meal_menu_number>";
            String custom_extract  = "<can_be_customized>([0-9]*)</can_be_customized>";

            String title    = AsyncHTTPTask.extractFromXml(title_extract, xml);
            String custom   = AsyncHTTPTask.extractFromXml(custom_extract, xml);
            String meal_num = AsyncHTTPTask.extractFromXml(mealnum_extract, xml);

            _Meal object = new _Meal();
            object.setMeal(title);
            object.setMealNumber(meal_num);
            object.setCustomizable(custom == "true");

            return object;
        }
    }

    /**
     * XXX
     * @param restaurant_id
     * @return
     */
    public String[] getCategories(int restaurant_id)
    {
        String[] categories;
        String get_uri = "/menu-categories/index/restaurant_id/" + restaurant_id + "/format/xml";

        /* get restaurants list. */
        String response = executeHTTPRequest(get_uri);

        List<String> matches = new ArrayList<String>();
        String menu_part  = "(<menu_title>)(.*?)(</menu_title>)";
        String cat_part   = "(<title>)(.*?)(</title>)";
        Pattern pattern = Pattern.compile("(<item>)(.*?)" + cat_part + "(.*?)" + menu_part + "(.*?)(</item>)");
        Matcher matcher = pattern.matcher(response);

        while (matcher.find())
            matches.add(matcher.group(4));

        categories = matches.toArray(new String[0]);
        return categories;
    }

    /**
     * XXX
     * @param selected_category
     * @return
     */
    public String[] getMeals(String selected_category)
    {
        String xml = getCategoryXML(selected_category);
        String id  = AsyncHTTPTask.extractFromXml("<id_category>([0-9]*)</id_category>", xml);

        String[] meals;
        String get_uri = "/menu-meals/index/category_id/" + id + "/format/xml";

        /* get restaurants list. */
        String response = executeHTTPRequest(get_uri);

        List<String> matches = new ArrayList<String>();
        String meal_part  = "(<title>)(.*?)(</title>)";
        String cat_part   = "(<category_title>)(.*?)(</category_title>)";
        Pattern pattern = Pattern.compile("(<item>)(.*?)" + meal_part + "(.*?)" + cat_part + "(.*?)(</item>)");
        Matcher matcher = pattern.matcher(response);

        while (matcher.find())
            matches.add(matcher.group(4));

        meals = matches.toArray(new String[0]);
        return meals;
    }

    /**
     * XXX
     * @param selected_meal
     * @return
     */
    public String[] getCustomizations(String selected_meal)
    {
        String meal_xml = getMealXML(selected_meal);
        String mid      = AsyncHTTPTask.extractFromXml("<id_item>([0-9]*)</id_item>", meal_xml);

        String[] meals;
        String get_uri = "/menu-customizations/index/meal_id/" + mid + "/format/xml";

        /* get restaurants list. */
        String response = executeHTTPRequest(get_uri);

        List<String> matches = new ArrayList<String>();
        String meal_part  = "(<title>)(.*?)(</title>)";
        String price_part = "(<price>)(.*?)(</price>)";
        Pattern pattern = Pattern.compile("(<item>)(.*?)" + meal_part + "(.*?)" + price_part + "(.*?)(</item>)");
        Matcher matcher = pattern.matcher(response);

        while (matcher.find())
            matches.add(matcher.group(4) + " (+ " + matcher.group(8) + " €)");

        meals = matches.toArray(new String[0]);
        return meals;
    }

    /**
     * XXX
     * @param title
     * @return
     */
    public String getCategoryXML(String title)
    {
        String get_uri = "/menu-categories/get/title/";
        get_uri += SecureData.getInstance().encrypt(title);
        get_uri += "/format/xml";

        String response = executeHTTPRequest(get_uri);
        return response;
    }

    /**
     * XXX
     * @param title
     * @return
     */
    public String getMealXML(String title)
    {
        String get_uri = "/menu-meals/get/title/";
        get_uri += SecureData.getInstance().encrypt(title);
        get_uri += "/format/xml";

        String response = executeHTTPRequest(get_uri);
        return response;
    }
}