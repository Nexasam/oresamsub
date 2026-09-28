package local.mymtn.extractor;

import android.app.Activity;
import android.graphics.Typeface;
import android.os.Bundle;
import android.text.method.ScrollingMovementMethod;
import android.view.Gravity;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.reactnativekeysjsi.PrivateKey;

import org.json.JSONObject;

public final class MainActivity extends Activity {
    private static final String[] ALLOWED_KEYS = {
        "AUTH0_CLIENT_ID",
        "AUTH0_AUDIENCE",
        "AUTH0_SCOPE",
        "AUTH0_DOMAIN",
        "mtndxl_customer_balance",
        "mtn_dxl_bundle_listing",
        "dxl_magento_bundle_filter",
        "mtn_dxl_susbcription_new",
        "mtn_dxl_customer_subscripation",
        "mtn_dxl_bank_list",
        "dxl_bundle_eligibility_listing",
        "mtn_dxl_product_eligibility_check_id",
        "dxl_microservice_share_airtime",
        "mtn_dxl_shared",
        "mtn_share_sme_url",
        "mtn_dxl_transaction_history",
        "mtndxl_payment_history_url"
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        LinearLayout layout = new LinearLayout(this);
        layout.setOrientation(LinearLayout.VERTICAL);
        layout.setGravity(Gravity.CENTER_HORIZONTAL);
        layout.setPadding(48, 72, 48, 48);

        TextView heading = new TextView(this);
        heading.setText("myMTN required runtime config");
        heading.setTextSize(20);
        heading.setTypeface(Typeface.DEFAULT_BOLD);
        layout.addView(heading);

        TextView value = new TextView(this);
        value.setTextSize(16);
        value.setTextIsSelectable(true);
        value.setMovementMethod(new ScrollingMovementMethod());
        value.setPadding(0, 32, 0, 0);
        layout.addView(value);

        try {
            String decodedJson = DecoderBridge.decode(PrivateKey.privatekey);
            JSONObject decoded = new JSONObject(decodedJson);
            StringBuilder result = new StringBuilder();

            for (String key : ALLOWED_KEYS) {
                String extractedValue = decoded.optString(key, "");
                result.append(key).append("=");
                if (extractedValue.isEmpty()) {
                    result.append("<missing>");
                } else {
                    result.append(extractedValue);
                }
                result.append("\n\n");
            }

            value.setText(result.toString().trim());
        } catch (Throwable error) {
            value.setText("Extraction failed: " + error.getClass().getSimpleName() + ": " + error.getMessage());
        }

        setContentView(layout);
    }
}
