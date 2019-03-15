package be.evias.eRemote.ctrl;

import be.evias.eRemote.lib.AbstractController;
import be.evias.eRemote.lib.AsyncHTTPTask;
import be.evias.eRemote.lib.SecureData;

public class AuthController
    extends AbstractController
{
    public class _Identity
        extends Object
    {
        private boolean is_valid = false;

        private String id = "";
        private String login = "";
        private String email = "";
        private String realname = "";

        _Identity(String i, String l, String e, String r)
        {
            if (i.isEmpty() && l.isEmpty() && e.isEmpty() && r.isEmpty())
                return ;

            id = i;
            login = l;
            email = e;
            realname = r;
            is_valid = true;
        }

        public boolean isValid() { return is_valid; }
        public String getId() { return id; }
        public String getLogin() { return id; }
        public String getEmail() { return id; }
        public String getRealname() { return id; }
    }

    public _Identity checkAuthentication(String uname, String ucred)
    {
        String auth_uri = "/user/login";
        auth_uri += "/identifier/" + SecureData.getInstance().encrypt(uname);
        auth_uri += "/credential/" + SecureData.getInstance().encrypt(ucred);
        auth_uri += "/format/xml";

        /* execute SECURED (AES/128) HTTP request. */
        String response = executeHTTPRequest(auth_uri);

        if (response.matches(".*<result>true</result>.*")) {
            /* Interpret response. (fill session manager object) */

            String id       = AsyncHTTPTask.extractFromXml("<id>([0-9]*)</id>", response);
            String login    = AsyncHTTPTask.extractFromXml("<login>([A-Za-z0-9_\\-\\.]*)</login>", response);
            String email    = AsyncHTTPTask.extractFromXml("<email>([A-Za-z0-9_\\-\\.@]*)</email>", response);
            String realname = AsyncHTTPTask.extractFromXml("<realname>([A-Za-z0-9_\\-\\. ]*)</realname>", response);

            return new _Identity(id, login, email, realname);
        }

        return new _Identity("", "", "", "");
    }
}