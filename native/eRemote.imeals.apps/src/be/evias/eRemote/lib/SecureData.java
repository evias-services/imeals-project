package be.evias.eRemote.lib;

import android.util.Log;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;

/**
 * Special thanks to SeRPRo for: http://www.androidsnippets.com/encrypt-decrypt-between-android-and-php
 */
public class SecureData
{
    private static SecureData _instance = null;

    private SecretKeySpec   _sks;
    private IvParameterSpec _ivspec;
    private Cipher          _cipher;

    public static SecureData getInstance()
    {
        if (null == SecureData._instance)
            SecureData._instance = new SecureData();

        return SecureData._instance;
    }

    SecureData()
    {
        try {
            String iv  = "eRemote.evias.be";
            String key = "be.evias.eRemote";

            _ivspec = new IvParameterSpec(iv.getBytes());
            _sks    = new SecretKeySpec(key.getBytes(), "AES");

            _cipher = Cipher.getInstance("AES/CBC/NoPadding");
        }
        catch (Exception e) {
            Log.e("SecureData", "AES secret key spec error");
            e.printStackTrace();
        }
    }

    public String encrypt(String input)
    {
        return bytesToHex(getInstance().getEncoded(input));
    }

    public String decrypt(String input)
    {
        return new String(getInstance().getDecoded(input)).trim();
    }

    private byte[] getEncoded(String input)
    {
        byte[] encodedBytes = null;
        try {
            _cipher.init(Cipher.ENCRYPT_MODE, _sks, _ivspec);
            encodedBytes = _cipher.doFinal(padString(input).getBytes());

            return encodedBytes;
        }
        catch (Exception e) {
            Log.e("SecureData", "AES encryption error");
            e.printStackTrace();
        }

        return encodedBytes;
    }

    private byte[] getDecoded(String input)
    {
        byte[] decodedBytes = null;
        try {
            _cipher.init(Cipher.DECRYPT_MODE, _sks, _ivspec);
            decodedBytes = _cipher.doFinal(hexToBytes(input));

            return decodedBytes;
        }
        catch (Exception e) {
            Log.e("SecureData", "AES encryption error");
            e.printStackTrace();
        }

        return decodedBytes;
    }

    private static String bytesToHex(byte[] data)
    {
        if (data == null)
            return null;

        int len     = data.length;
        String str  = "";
        for (int i = 0; i < len; i++) {
            if ((data[i]&0xFF) < 16)
                str = str + "0" + java.lang.Integer.toHexString(data[i]&0xFF);
            else
                str = str + java.lang.Integer.toHexString(data[i]&0xFF);
        }
        return str;
    }

    private static byte[] hexToBytes(String str)
    {
        if (str == null)
            return null;
        else if (str.length() < 2)
            return null;
        else {
            int len = str.length() / 2;
            byte[] buffer = new byte[len];
            for (int i = 0; i < len; i++)
                buffer[i] = (byte) Integer.parseInt(str.substring(i * 2, i * 2 + 2),16);

            return buffer;
        }
    }

    private static String padString(String source)
    {
        char paddingChar = ' ';
        int size = 16;
        int x = source.length() % size;
        int padLength = size - x;

        for (int i = 0; i < padLength; i++)
            source += paddingChar;

        return source;
    }
}