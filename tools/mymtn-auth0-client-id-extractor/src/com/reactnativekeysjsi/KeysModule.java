package com.reactnativekeysjsi;

public final class KeysModule {
    static {
        System.loadLibrary("react-native-keys");
    }

    private KeysModule() {
    }

    public static native String getJniJsonStringifyData(String encryptedConfiguration);
}
