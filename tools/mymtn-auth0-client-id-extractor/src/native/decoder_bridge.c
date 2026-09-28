#include <jni.h>

extern JNIEXPORT jstring JNICALL
Java_com_reactnativekeysjsi_KeysModule_getJniJsonStringifyData(
    JNIEnv *env,
    jclass type,
    jstring encrypted_configuration
);

JNIEXPORT jint JNICALL JNI_OnLoad(JavaVM *vm, void *reserved) {
    (void) vm;
    (void) reserved;
    return JNI_VERSION_1_6;
}

JNIEXPORT jstring JNICALL
Java_local_mymtn_extractor_DecoderBridge_decode(
    JNIEnv *env,
    jclass type,
    jstring encrypted_configuration
) {
    return Java_com_reactnativekeysjsi_KeysModule_getJniJsonStringifyData(
        env,
        type,
        encrypted_configuration
    );
}
