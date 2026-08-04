<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>OresamSub API · Developer Documentation</title>
    <meta name="description" content="Integrate data, airtime, cable TV and electricity services into your website with the OresamSub Business API.">
    <style>
        :root{--ink:#10221b;--muted:#64746d;--green:#07865f;--lime:#dffc89;--paper:#f6f8f7;--line:#dfe8e4;--code:#091713}*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;color:var(--ink);background:#fff;font:15px/1.7 Inter,ui-sans-serif,system-ui,-apple-system,sans-serif}.top{position:sticky;top:0;z-index:10;border-bottom:1px solid var(--line);background:rgba(255,255,255,.92);backdrop-filter:blur(16px)}.topin{max-width:1280px;margin:auto;padding:15px 28px;display:flex;align-items:center;justify-content:space-between}.brand{font-weight:900;letter-spacing:-.04em;font-size:21px}.brand i{color:var(--green);font-style:normal}.version{padding:5px 9px;border-radius:20px;background:#e9f8f2;color:#076548;font-size:11px;font-weight:800}.layout{max-width:1280px;margin:auto;display:grid;grid-template-columns:220px minmax(0,1fr) 400px;gap:46px;padding:42px 28px 100px}.side{position:sticky;top:90px;height:max-content}.side b{display:block;margin:18px 0 8px;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:#87958f}.side a{display:block;padding:6px 0;color:var(--muted);text-decoration:none}.side a:hover{color:var(--green)}main{min-width:0}.hero{padding:48px;border-radius:28px;background:linear-gradient(135deg,#092d23,#075f48);color:#fff;box-shadow:0 24px 70px rgba(4,66,48,.18)}.eyebrow{color:var(--lime);font-weight:800;text-transform:uppercase;letter-spacing:.13em;font-size:11px}.hero h1{font-size:clamp(38px,5vw,64px);line-height:1.02;letter-spacing:-.06em;margin:18px 0}.hero p{max-width:650px;color:#d6eee5;font-size:17px}.base{display:inline-flex;margin-top:16px;padding:10px 14px;border:1px solid #3c7867;border-radius:10px;background:#08241c;font:12px ui-monospace,SFMono-Regular,monospace}.section{padding-top:68px}.section h2{font-size:30px;letter-spacing:-.035em;margin:0 0 10px}.section>p{color:var(--muted)}.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:24px}.step{padding:20px;border:1px solid var(--line);border-radius:16px}.step span{display:grid;place-items:center;width:28px;height:28px;border-radius:8px;background:var(--lime);font-weight:900}.step h3{margin:14px 0 4px}.step p{margin:0;color:var(--muted);font-size:13px}.endpoint{margin:18px 0;padding:20px;border:1px solid var(--line);border-radius:16px}.method{display:inline-block;margin-right:10px;color:#066646;background:#dcf7ec;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:900}.method.post{color:#714e00;background:#fff0b8}.endpoint code{font-weight:800}.endpoint p{color:var(--muted);margin-bottom:0}.notice{padding:17px 20px;border-left:5px solid #e5b429;background:#fff9e7;border-radius:10px}.codepane{position:sticky;top:90px;height:max-content;border-radius:20px;overflow:hidden;background:var(--code);color:#cce4da;box-shadow:0 20px 50px rgba(0,0,0,.16)}.codetop{padding:13px 17px;border-bottom:1px solid #203a31;color:#8eb5a6;font-size:12px}.codepane pre{margin:0;padding:22px;overflow:auto;white-space:pre-wrap;font:12px/1.7 ui-monospace,SFMono-Regular,Menlo,monospace}.try{padding:18px;border-top:1px solid #203a31}.try label{display:block;margin:8px 0 4px;color:#8eb5a6;font-size:11px}.try input,.try select,.try textarea{width:100%;padding:10px;border:1px solid #315044;border-radius:8px;color:#fff;background:#102820}.try textarea{min-height:145px;resize:vertical;font:11px/1.5 ui-monospace,SFMono-Regular,monospace}.try button{width:100%;margin-top:12px;padding:11px;border:0;border-radius:9px;background:var(--lime);color:#173124;font-weight:900;cursor:pointer}.try small{display:block;margin-top:8px;color:#79988c}.response{max-height:230px;margin-top:12px!important;border-top:1px solid #203a31}.flow-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:24px}.flow-card{min-width:0;border:1px solid var(--line);border-radius:18px;overflow:hidden;background:#fff}.flow-head{padding:18px 20px;background:#eff9f5}.flow-head h3{margin:0 0 4px}.flow-head p{margin:0;color:var(--muted);font-size:13px}.flow-step{padding:18px 20px;border-top:1px solid var(--line)}.flow-step strong{display:block;margin-bottom:9px}.flow-endpoint{display:flex;align-items:center;gap:8px;margin-bottom:10px}.code-block{margin:0;max-width:100%;padding:16px;border-radius:11px;overflow:auto;color:#cce4da;background:var(--code);white-space:pre;font:11px/1.65 ui-monospace,SFMono-Regular,Menlo,monospace}.example-group{margin-top:28px}.example-card{margin:14px 0;border:1px solid var(--line);border-radius:16px;overflow:hidden;background:#fff}.example-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:13px 16px;background:#f7faf9}.example-head strong{font-size:13px}.status{padding:4px 8px;border-radius:20px;font-size:10px;font-weight:900}.status.ok{color:#075f48;background:#dff8ed}.status.bad{color:#9b2c2c;background:#fee2e2}.example-card pre{margin:0;max-width:100%;padding:18px;overflow:auto;color:#cce4da;background:var(--code);white-space:pre;font:11px/1.65 ui-monospace,SFMono-Regular,Menlo,monospace}@media(max-width:1050px){.layout{grid-template-columns:180px 1fr}.codepane{position:relative;top:auto;grid-column:2}}@media(max-width:720px){.layout{display:block;padding:22px 16px 70px}.side{display:none}.hero{padding:30px}.steps,.flow-grid{grid-template-columns:1fr}.codepane{margin-top:36px}.topin{padding:13px 16px}.example-head{align-items:flex-start;flex-direction:column}}
    </style>
</head>
<body>
<header class="top"><div class="topin"><div class="brand">Oresam<i>Sub</i> API</div><span class="version">VERSION 2.0</span></div></header>
<div class="layout">
    <aside class="side"><b>Start</b><a href="#overview">Overview</a><a href="#quickstart">Quick start</a><a href="#authentication">Authentication</a><b>Reference</b><a href="#catalogue">Catalogue</a><a href="#wallet">Wallet</a><a href="#validation">Validate customer</a><a href="#purchase">Buy service</a><a href="#status">Transaction status</a><b>Guides</b><a href="#response-examples">Response examples</a><a href="#idempotency">Safe retries</a><a href="{{ route('api.v2.openapi') }}">OpenAPI 3.1 ↗</a></aside>
    <main>
        <section class="hero" id="overview"><span class="eyebrow">Built for digital-service businesses</span><h1>One API.<br>Every transaction.</h1><p>Connect your website to OresamSub with a predictable API for data, airtime, cable TV, electricity, wallet balance and transaction reconciliation.</p><span class="base">https://oresamsub.com/api/v2</span></section>
        <section class="section" id="quickstart"><h2>From token to first purchase</h2><p>Every plan and price comes from your OresamSub account. Never hard-code plan prices.</p><div class="steps"><div class="step"><span>1</span><h3>Get a token</h3><p>Generate your API token from your account and keep it on your server.</p></div><div class="step"><span>2</span><h3>Sync plans</h3><p>Fetch the catalogue and store the returned external plan IDs.</p></div><div class="step"><span>3</span><h3>Submit safely</h3><p>Send a unique reference, then reconcile it if your request times out.</p></div></div></section>
        <section class="section" id="authentication"><h2>Authentication</h2><p>Send the token as a Bearer credential. API tokens must never be placed in browser JavaScript, URLs or public repositories.</p><div class="notice"><b>Server-side only.</b> Requests to OresamSub should originate from your application server, not directly from a customer’s browser.</div></section>
        <section class="section" id="catalogue"><h2>Endpoints</h2><div class="endpoint"><span class="method">GET</span><code>/api/v2/catalogue</code><p>Returns active public data, airtime, cable and electricity plans at your account’s current price.</p></div><div class="endpoint" id="wallet"><span class="method">GET</span><code>/api/v2/wallet</code><p>Returns your available main-wallet balance in NGN.</p></div><div class="endpoint" id="validation"><span class="method post">POST</span><code>/api/v2/validate-customer</code><p>Validates a cable smartcard/IUC or electricity meter and returns a validation reference that remains valid for 10 minutes.</p></div><div class="endpoint" id="purchase"><span class="method post">POST</span><code>/api/v2/buy-service</code><p>Purchases data, airtime, cable or electricity through one consistent endpoint. Cable and electricity require a recent validation reference.</p></div><div class="endpoint" id="status"><span class="method">GET</span><code>/api/v2/transactions/{reference}</code><p>Returns only a transaction owned by the authenticated business. Successful electricity responses include the meter token when supplied by the provider.</p></div></section>
        <section class="section" id="biller-flow"><h2>Cable and electricity flow</h2><p>Both services use the same two endpoints: validate the customer first, then submit the purchase with the returned <code>validation_reference</code>.</p><div class="flow-grid">
            <article class="flow-card"><div class="flow-head"><h3>Cable TV</h3><p>Validate a smartcard or IUC before subscription.</p></div><div class="flow-step"><strong>Step 1 · Validate customer</strong><div class="flow-endpoint"><span class="method post">POST</span><code>/api/v2/validate-customer</code></div><pre class="code-block">{
  "service": "cable",
  "plan_id": 2101,
  "customer_number": "1234567890"
}</pre></div><div class="flow-step"><strong>Step 2 · Buy cable plan</strong><div class="flow-endpoint"><span class="method post">POST</span><code>/api/v2/buy-service</code></div><pre class="code-block">{
  "service": "cable",
  "plan_id": 2101,
  "customer_number": "1234567890",
  "validation_reference": "VAL-EXAMPLE",
  "reference": "ORDER-10003"
}</pre></div></article>
            <article class="flow-card"><div class="flow-head"><h3>Electricity</h3><p>Validate a meter before purchasing units.</p></div><div class="flow-step"><strong>Step 1 · Validate customer</strong><div class="flow-endpoint"><span class="method post">POST</span><code>/api/v2/validate-customer</code></div><pre class="code-block">{
  "service": "electricity",
  "plan_id": 3101,
  "customer_number": "01234567890"
}</pre></div><div class="flow-step"><strong>Step 2 · Buy electricity</strong><div class="flow-endpoint"><span class="method post">POST</span><code>/api/v2/buy-service</code></div><pre class="code-block">{
  "service": "electricity",
  "plan_id": 3101,
  "customer_number": "01234567890",
  "amount": 5000,
  "validation_reference": "VAL-EXAMPLE",
  "reference": "ORDER-10004"
}</pre></div></article>
        </div><div class="notice" style="margin-top:18px"><b>Validation references expire after 10 minutes.</b> They are locked to the authenticated business, selected plan and customer number.</div></section>
        <section class="section" id="response-examples"><h2>Response examples</h2><p>Formatted examples of the stable JSON envelope your integration should handle.</p><div class="example-group"><h3>Success responses</h3>
            <article class="example-card"><div class="example-head"><strong>GET /api/v2/catalogue</strong><span class="status ok">200 SUCCESS</span></div><pre>{
  "success": true,
  "message": "Catalogue fetched successfully.",
  "data": [{"id": 1201, "service": "data", "name": "1GB Monthly", "network": "MTN", "price": 450}],
  "meta": null,
  "errors": null
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>GET /api/v2/wallet</strong><span class="status ok">200 SUCCESS</span></div><pre>{
  "success": true,
  "message": "Wallet fetched successfully.",
  "data": {"currency": "NGN", "available_balance": 12500.50},
  "meta": null,
  "errors": null
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>POST /api/v2/validate-customer</strong><span class="status ok">200 SUCCESS</span></div><pre>{
  "success": true,
  "message": "Customer validated successfully.",
  "data": {
    "validation_reference": "VAL-EXAMPLE",
    "customer_name": "Test Customer",
    "address": "Ibadan",
    "expires_at": "2026-08-04T14:10:00+01:00"
  },
  "meta": null,
  "errors": null
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>POST /api/v2/buy-service</strong><span class="status ok">200 SUCCESS</span></div><pre>{
  "success": true,
  "message": "Transaction processed successfully.",
  "data": {
    "reference": "ORDER-10004",
    "status": "successful",
    "service": "electricity",
    "customer_number": "01234567890",
    "amount": 5000,
    "token": "1234-5678-9012"
  },
  "meta": null,
  "errors": null
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>GET /api/v2/transactions/{reference}</strong><span class="status ok">200 SUCCESS</span></div><pre>{
  "success": true,
  "message": "Transaction fetched successfully.",
  "data": {"reference": "ORDER-10001", "status": "successful", "service": "data", "customer_number": "08030000000", "amount": 450},
  "meta": null,
  "errors": null
}</pre></article></div>
            <div class="example-group"><h3>Failure responses</h3>
            <article class="example-card"><div class="example-head"><strong>Invalid or missing API token</strong><span class="status bad">401 FAILED</span></div><pre>{
  "success": false,
  "message": "Authentication failed. Provide a valid Bearer API token.",
  "data": null,
  "meta": null,
  "errors": {"authentication": ["The supplied API token is invalid."]}
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>Invalid request information</strong><span class="status bad">422 FAILED</span></div><pre>{
  "success": false,
  "message": "Please check the provided information.",
  "data": null,
  "meta": null,
  "errors": {"customer_number": ["Provide a valid Nigerian mobile number."]}
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>Expired bill validation</strong><span class="status bad">422 FAILED</span></div><pre>{
  "success": false,
  "message": "The validation reference is invalid, expired or does not match this purchase.",
  "data": null,
  "meta": null,
  "errors": {"validation_reference": ["Validate the customer again before purchasing."]}
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>Reference already used for different details</strong><span class="status bad">409 CONFLICT</span></div><pre>{
  "success": false,
  "message": "This reference has already been used for a different transaction.",
  "data": null,
  "meta": null,
  "errors": {"reference": ["Use a new unique reference."]}
}</pre></article>
            <article class="example-card"><div class="example-head"><strong>Transaction reference not found</strong><span class="status bad">404 NOT FOUND</span></div><pre>{
  "success": false,
  "message": "Transaction not found.",
  "data": null,
  "meta": null,
  "errors": {"reference": ["No transaction matches this reference."]}
}</pre></article></div>
        </section>
        <section class="section" id="idempotency"><h2>Safe retries and references</h2><p>Create a unique reference for every customer order. If a connection times out, query the transaction endpoint before retrying.</p><p>Submitting the same reference with the same purchase details returns the existing transaction. Reusing it with different details returns <code>409 Conflict</code>.</p><div class="notice">Public statuses are <b>pending</b>, <b>processing</b>, <b>successful</b>, <b>failed</b> and <b>reversed</b>.</div></section>
    </main>
    <aside class="codepane"><div class="codetop" id="curlTitle">cURL · GET /catalogue</div><pre id="curlPreview" aria-live="polite"></pre><div class="try"><b>Test every endpoint</b><label>API token</label><input id="token" type="password" autocomplete="off" placeholder="Kept only in this browser tab"><label>Operation</label><select id="testEndpoint"><option value="catalogue">GET /catalogue</option><option value="wallet">GET /wallet</option><option value="validate_cable">Validate cable customer</option><option value="validate_electricity">Validate electricity customer</option><option value="buy_data">Buy data</option><option value="buy_airtime">Buy airtime</option><option value="buy_cable">Buy cable</option><option value="buy_electricity">Buy electricity</option><option value="transaction">Transaction lookup</option></select><div id="payloadWrap" hidden><label>Request JSON</label><textarea id="requestPayload" spellcheck="false"></textarea></div><button id="send">Send test request</button><small>For security, the token is never saved. Purchase tests can debit your live wallet.</small></div><pre class="response" id="response">Response will appear here.</pre></aside>
</div>
<script>
const operations={
catalogue:{method:'GET',path:'catalogue'},wallet:{method:'GET',path:'wallet'},
validate_cable:{method:'POST',path:'validate-customer',body:{service:'cable',plan_id:2101,customer_number:'1234567890'}},
validate_electricity:{method:'POST',path:'validate-customer',body:{service:'electricity',plan_id:3101,customer_number:'01234567890'}},
buy_data:{method:'POST',path:'buy-service',purchase:true,body:{service:'data',plan_id:1201,customer_number:'08030000000',reference:'ORDER-10001'}},
buy_airtime:{method:'POST',path:'buy-service',purchase:true,body:{service:'airtime',plan_id:1401,customer_number:'08030000000',amount:1000,reference:'ORDER-10002'}},
buy_cable:{method:'POST',path:'buy-service',purchase:true,body:{service:'cable',plan_id:2101,customer_number:'1234567890',validation_reference:'VAL-PASTE-REFERENCE',reference:'ORDER-10003'}},
buy_electricity:{method:'POST',path:'buy-service',purchase:true,body:{service:'electricity',plan_id:3101,customer_number:'01234567890',amount:5000,validation_reference:'VAL-PASTE-REFERENCE',reference:'ORDER-10004'}},
transaction:{method:'GET',path:'transactions',body:{reference:'ORDER-10001'}}
};
const select=document.getElementById('testEndpoint'),wrap=document.getElementById('payloadWrap'),payload=document.getElementById('requestPayload'),box=document.getElementById('response'),curlPreview=document.getElementById('curlPreview'),curlTitle=document.getElementById('curlTitle');
function updateCurlPreview(){const operation=operations[select.value];let body=null;try{body=operation.body?JSON.parse(payload.value):null}catch{curlPreview.textContent='Fix the request JSON to generate a cURL example.';return}let path=operation.path;if(select.value==='transaction')path+='/'+encodeURIComponent(body?.reference||'{reference}');const lines=[`curl --request ${operation.method}`,`  https://oresamsub.com/api/v2/${path}`,`  --header "Authorization: Bearer YOUR_API_TOKEN"`];if(body&&select.value!=='transaction'){lines.push('  --header "Content-Type: application/json"');lines.push(`  --data '${JSON.stringify(body,null,2)}'`)}const continuation=' '+String.fromCharCode(92);curlPreview.textContent=lines.map((line,index)=>index<lines.length-1?line+continuation:line).join('\n');curlTitle.textContent='cURL · '+select.options[select.selectedIndex].text}
function updateTester(){const operation=operations[select.value];wrap.hidden=!operation.body;payload.value=operation.body?JSON.stringify(operation.body,null,2):'';updateCurlPreview()}select.addEventListener('change',updateTester);payload.addEventListener('input',updateCurlPreview);updateTester();
document.getElementById('send').addEventListener('click',async()=>{const token=document.getElementById('token').value,operation=operations[select.value];if(!token){box.textContent='Enter an API token first.';return}let body=null;try{body=operation.body?JSON.parse(payload.value):null}catch(error){box.textContent='Request JSON is invalid: '+error.message;return}if(operation.purchase&&!confirm('This is a live purchase test and may debit your wallet. Continue?'))return;let path=operation.path;if(select.value==='transaction'){if(!body.reference){box.textContent='Enter a transaction reference.';return}path+='/'+encodeURIComponent(body.reference);body=null}box.textContent='Sending…';try{const options={method:operation.method,headers:{Accept:'application/json',Authorization:'Bearer '+token}};if(body){options.headers['Content-Type']='application/json';options.body=JSON.stringify(body)}const response=await fetch('/api/v2/'+path,options);const text=await response.text();let output;try{output=JSON.parse(text)}catch{output=text}box.textContent='HTTP '+response.status+'\n'+(typeof output==='string'?output:JSON.stringify(output,null,2))}catch(error){box.textContent='Request failed: '+error.message}});
</script>
</body>
</html>
