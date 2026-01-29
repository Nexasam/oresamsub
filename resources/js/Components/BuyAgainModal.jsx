// resources/js/Components/BuyAgainModal.jsx
import { useState, useEffect } from "react";
import axios from "axios";
import Swal from "sweetalert2";

export default function BuyAgainModal({ isOpen, onClose, contacts }) {
  const [favorites, setFavorites] = useState([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedPlan, setSelectedPlan] = useState(null);
  const [phoneNumber, setPhoneNumber] = useState("");
  const [filteredContacts, setFilteredContacts] = useState([]);
  const [showContactDropdown, setShowContactDropdown] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    if (isOpen) {
      axios.get(route("user.favorite_data")).then((res) => {
        setFavorites(res.data || []);
      });
    }
  }, [isOpen]);

  // Contact search
  const handlePhoneChange = (value) => {
    setPhoneNumber(value);

    if (!value || value.length < 2) {
      setFilteredContacts([]);
      setShowContactDropdown(false);
      return;
    }

    const term = value.toLowerCase();
    const matches = contacts.filter(
      (c) =>
        c.phone_number.includes(term) ||
        (c.name && c.name.toLowerCase().includes(term))
    );

    setFilteredContacts(matches.slice(0, 8));
    setShowContactDropdown(matches.length > 0);
  };

  const selectContact = (contact) => {
    setPhoneNumber(contact.phone_number);
    setShowContactDropdown(false);
  };

  const filteredFavorites = favorites.filter(
    (f) =>
      f.product_plan_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      f.network_name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleBuy = async () => {
    if (!selectedPlan) {
      Swal.fire("No Plan Selected", "Please select a plan first.", "warning");
      return;
    }

    if (!phoneNumber || phoneNumber.trim().length < 10) {
      Swal.fire("Invalid Number", "Please enter a valid phone number.", "warning");
      return;
    }

    // Check if contact exists
    const existingContact = contacts.find(
      (c) => c.phone_number.replace(/\s/g, "") === phoneNumber.replace(/\s/g, "")
    );

    let saveContactPayload = null;

    // Prompt to save contact if unsaved or unnamed
    if (!existingContact || !existingContact.name || existingContact.name.trim() === "") {
      const { value: contactName } = await Swal.fire({
        title: "Save this contact?",
        html: `
          <p class="text-sm mb-2">
            This number ${!existingContact ? "is not in your saved contacts" : "has no name"}.
          </p>
          <input
            id="contact-name"
            class="swal2-input"
            placeholder="Contact name (optional)"
          />
          <p class="text-xs text-gray-500 mt-2">
            You can skip this if you don't want to save it.
          </p>
        `,
        showCancelButton: true,
        confirmButtonText: "Save & Continue",
        cancelButtonText: "Skip",
        focusConfirm: false,
        preConfirm: () => document.getElementById("contact-name").value,
      });

      if (contactName && contactName.trim() !== "") {
        saveContactPayload = {
          phone_number: phoneNumber,
          name: contactName.trim(),
        };
      }
    }

    const result = await Swal.fire({
      title: "Confirm Purchase",
      html: `
        Are you sure you want to purchase 
        <b>${selectedPlan.product_plan_name}</b> 
        for <b>₦${Number(selectedPlan.selling_price).toLocaleString("en-NG")}</b> 
        to <b>${phoneNumber}</b>?
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Yes, Buy Now ✅",
      cancelButtonText: "Cancel ❌",
      confirmButtonColor: "#059669",
      cancelButtonColor: "#d33",
    });

    if (!result.isConfirmed) return;

    try {
      setSubmitting(true);
      const response = await axios.post(route("user.data.buy_again_data_action"), {
        product_plan_id: selectedPlan.product_plan_id,
        phone_number: phoneNumber,
        wallet_category: "main_wallet",
        pin: "", // optional PIN handling if needed
        save_contact: saveContactPayload,
        validatephonenetwork: 0,
      });

      if (response.data.status === 1) {
        Swal.fire("✅ Success", response.data.message, "success");
        onClose();
      } else {
        Swal.fire("⚠️ Failed", response.data.message, "error");
      }
    } catch (error) {
      console.error(error);
      Swal.fire("❌ Error", "Something went wrong. Please try again.", "error");
    } finally {
      setSubmitting(false);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl relative">
        <button
          className="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:hover:text-white"
          onClick={onClose}
        >
          ✕
        </button>

        <h2 className="text-lg font-semibold mb-4">Buy Again</h2>

        <input
          type="text"
          placeholder="Search favorite plans..."
          className="w-full border rounded-lg p-2 mb-3"
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
        />

        <div className="max-h-64 overflow-y-auto mb-3">
          {filteredFavorites.map((f) => (
            <div
              key={f.product_plan_id}
              onClick={() => setSelectedPlan(f)}
              className={`p-3 mb-2 border rounded-lg cursor-pointer transition ${
                selectedPlan?.product_plan_id === f.product_plan_id
                  ? "border-emerald-600 bg-emerald-50 dark:bg-emerald-900/30"
                  : "hover:border-emerald-400"
              }`}
            >
              <div className="font-semibold text-gray-800 dark:text-white">{f.product_plan_name}</div>
              <div className="text-sm text-gray-500">{f.network_name}</div>
              <div className="text-emerald-600 dark:text-emerald-400 font-bold">
                ₦{Number(f.selling_price).toLocaleString("en-NG")}
              </div>
            </div>
          ))}

              {favorites.length === 0 && (
                <div className="text-center mb-3">
                  <a
                    href={route("user.buy_data")}
                    className="text-emerald-600 hover:underline font-semibold"
                  >
                    No favorite plans yet? Buy data here
                  </a>
                </div>
              )}

        </div>

        <div className="relative">
          <input
            type="tel"
            placeholder="Enter phone number"
            className="w-full border rounded-lg p-2 mb-3"
            value={phoneNumber}
            onChange={(e) => handlePhoneChange(e.target.value)}
            onFocus={() => filteredContacts.length && setShowContactDropdown(true)}
            onBlur={() => setTimeout(() => setShowContactDropdown(false), 150)}
          />

          {/* CONTACT SUGGESTIONS */}
          {showContactDropdown && (
            <div className="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
              {filteredContacts.map((contact) => (
                <div
                  key={contact.id}
                  onClick={() => selectContact(contact)}
                  className="px-3 py-2 cursor-pointer hover:bg-emerald-50 dark:hover:bg-gray-700 transition"
                >
                  <div className="text-sm font-medium text-gray-800 dark:text-white">
                    {contact.name || "Unnamed contact"}
                  </div>
                  <div className="text-xs text-gray-500">
                    {contact.phone_number}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        <button
          onClick={handleBuy}
          disabled={submitting}
          className="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition disabled:opacity-50"
        >
          {submitting ? "Processing..." : "📶 Buy Now"}
        </button>
      </div>
    </div>
  );
}
