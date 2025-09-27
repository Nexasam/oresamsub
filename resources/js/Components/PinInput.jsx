import { useState, useRef } from "react";

export default function PinInput({ value, onChange }) {
  const [pin, setPin] = useState(value || ["", "", "", ""]);
  const inputs = useRef([]);

  const handleChange = (e, index) => {
    const val = e.target.value.replace(/\D/, ""); // only digits
    if (!val) return;

    const newPin = [...pin];
    newPin[index] = val;
    setPin(newPin);
    onChange(newPin.join(""));

    // Move focus to next input
    if (index < inputs.current.length - 1) {
      inputs.current[index + 1].focus();
    }
  };

  const handleKeyDown = (e, index) => {
    if (e.key === "Backspace" && !pin[index] && index > 0) {
      const newPin = [...pin];
      newPin[index - 1] = "";
      setPin(newPin);
      inputs.current[index - 1].focus();
    }
  };

  return (
    <div>
      <label className="block text-sm mb-2">Transaction PIN</label>
      <div className="flex space-x-3 justify-center">
        {pin.map((num, i) => (
          <input
            key={i}
            ref={(el) => (inputs.current[i] = el)}
            type="password"
            maxLength={1}
            value={num}
            onChange={(e) => handleChange(e, i)}
            onKeyDown={(e) => handleKeyDown(e, i)}
            className="w-14 h-14 text-center text-xl rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 transition"
          />
        ))}
      </div>
    </div>
  );
}
