package local.mymtn.extractor;

final class DecoderBridge {
    static {
        System.loadLibrary("mymtn-config-extractor");
    }

    private DecoderBridge() {
    }

    static native String decode(String encryptedConfiguration);
}
