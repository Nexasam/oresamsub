#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/../.." && pwd)"
TOOL_DIR="$ROOT_DIR/tools/mymtn-auth0-client-id-extractor"
SDK_DIR="${ANDROID_SDK_ROOT:-$HOME/Library/Android/sdk}"
PLATFORM_JAR="$SDK_DIR/platforms/android-35/android.jar"
BUILD_TOOLS="$SDK_DIR/build-tools/35.0.1"
NDK_DIR="${ANDROID_NDK_ROOT:-$SDK_DIR/ndk/26.3.11579264}"
NDK_CLANG="$NDK_DIR/toolchains/llvm/prebuilt/darwin-x86_64/bin/armv7a-linux-androideabi26-clang"
DECOMPILED_PRIVATE_KEY="/tmp/ttn-jadx/sources/com/reactnativekeysjsi/PrivateKey.java"
SPLIT_APK="$HOME/Downloads/ttn-apks/split_config.armeabi_v7a.apk"
OUTPUT_DIR="$ROOT_DIR/storage/app/private/api-research/mymtn"
OUTPUT_APK="$OUTPUT_DIR/mymtn-runtime-config-extractor.apk"
BUILD_DIR="$(mktemp -d /tmp/mymtn-client-id-extractor.XXXXXX)"

JAVA_HOME_17=""
if [[ -x "/usr/libexec/java_home" ]]; then
    JAVA_HOME_17="$(/usr/libexec/java_home -v 17 2>/dev/null || true)"
fi

if [[ -n "${JAVA_HOME:-}" && -x "$JAVA_HOME/bin/javac" ]]; then
    JAVAC="$JAVA_HOME/bin/javac"
elif [[ -n "$JAVA_HOME_17" && -x "$JAVA_HOME_17/bin/javac" ]]; then
    export JAVA_HOME="$JAVA_HOME_17"
    JAVAC="$JAVA_HOME/bin/javac"
elif [[ -x "/usr/local/opt/openjdk@17/bin/javac" ]]; then
    export JAVA_HOME="/usr/local/opt/openjdk@17"
    JAVAC="$JAVA_HOME/bin/javac"
elif [[ -x "/opt/homebrew/opt/openjdk@17/bin/javac" ]]; then
    export JAVA_HOME="/opt/homebrew/opt/openjdk@17"
    JAVAC="$JAVA_HOME/bin/javac"
elif [[ -x "/Applications/Android Studio.app/Contents/jbr/Contents/Home/bin/javac" ]]; then
    export JAVA_HOME="/Applications/Android Studio.app/Contents/jbr/Contents/Home"
    JAVAC="$JAVA_HOME/bin/javac"
else
    echo "A Java 17+ JDK was not found. Install the Intel-compatible package with:" >&2
    echo "  brew install --cask temurin@17" >&2
    exit 1
fi

cleanup() {
    rm -rf "$BUILD_DIR"
}
trap cleanup EXIT

for required in "$PLATFORM_JAR" "$BUILD_TOOLS/aapt2" "$BUILD_TOOLS/d8" "$BUILD_TOOLS/zipalign" "$BUILD_TOOLS/apksigner" "$NDK_CLANG" "$DECOMPILED_PRIVATE_KEY" "$SPLIT_APK"; do
    if [[ ! -e "$required" ]]; then
        echo "Missing required file: $required" >&2
        exit 1
    fi
done

mkdir -p \
    "$BUILD_DIR/classes" \
    "$BUILD_DIR/dex" \
    "$BUILD_DIR/package/lib/armeabi-v7a" \
    "$BUILD_DIR/src/com/reactnativekeysjsi" \
    "$BUILD_DIR/src/local/mymtn/extractor" \
    "$OUTPUT_DIR"

cp "$TOOL_DIR/src/com/reactnativekeysjsi/KeysModule.java" "$BUILD_DIR/src/com/reactnativekeysjsi/KeysModule.java"
cp "$TOOL_DIR/src/local/mymtn/extractor/MainActivity.java" "$BUILD_DIR/src/local/mymtn/extractor/MainActivity.java"
cp "$TOOL_DIR/src/local/mymtn/extractor/DecoderBridge.java" "$BUILD_DIR/src/local/mymtn/extractor/DecoderBridge.java"
cp "$DECOMPILED_PRIVATE_KEY" "$BUILD_DIR/src/com/reactnativekeysjsi/PrivateKey.java"

"$JAVAC" \
    -source 8 \
    -target 8 \
    -classpath "$PLATFORM_JAR" \
    -d "$BUILD_DIR/classes" \
    "$BUILD_DIR/src/com/reactnativekeysjsi/KeysModule.java" \
    "$BUILD_DIR/src/com/reactnativekeysjsi/PrivateKey.java" \
    "$BUILD_DIR/src/local/mymtn/extractor/DecoderBridge.java" \
    "$BUILD_DIR/src/local/mymtn/extractor/MainActivity.java"

"$JAVA_HOME/bin/jar" cf "$BUILD_DIR/classes.jar" -C "$BUILD_DIR/classes" .

"$BUILD_TOOLS/d8" \
    --lib "$PLATFORM_JAR" \
    --min-api 26 \
    --output "$BUILD_DIR/dex" \
    "$BUILD_DIR/classes.jar"

unzip -q "$SPLIT_APK" 'lib/armeabi-v7a/*' -d "$BUILD_DIR/native"
cp "$BUILD_DIR/native/lib/armeabi-v7a/"*.so "$BUILD_DIR/package/lib/armeabi-v7a/"
"$NDK_CLANG" \
    -shared \
    -fPIC \
    -Wl,-soname,libmymtn-config-extractor.so \
    "$TOOL_DIR/src/native/decoder_bridge.c" \
    -L"$BUILD_DIR/package/lib/armeabi-v7a" \
    -Wl,--no-as-needed \
    -lreact-native-keys \
    -o "$BUILD_DIR/package/lib/armeabi-v7a/libmymtn-config-extractor.so"
cp "$BUILD_DIR/dex/classes.dex" "$BUILD_DIR/package/classes.dex"

"$BUILD_TOOLS/aapt2" link \
    -I "$PLATFORM_JAR" \
    --manifest "$TOOL_DIR/AndroidManifest.xml" \
    -o "$BUILD_DIR/unsigned-base.apk"

cp "$BUILD_DIR/unsigned-base.apk" "$BUILD_DIR/unsigned.apk"
(
    cd "$BUILD_DIR/package"
    zip -q -r "$BUILD_DIR/unsigned.apk" classes.dex lib
)

"$BUILD_TOOLS/zipalign" -f 4 "$BUILD_DIR/unsigned.apk" "$BUILD_DIR/aligned.apk"
"$BUILD_TOOLS/apksigner" sign \
    --ks "$HOME/.android/debug.keystore" \
    --ks-pass pass:android \
    --key-pass pass:android \
    --out "$OUTPUT_APK" \
    "$BUILD_DIR/aligned.apk"

"$BUILD_TOOLS/apksigner" verify --verbose "$OUTPUT_APK"
echo "$OUTPUT_APK"
