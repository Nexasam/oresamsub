  // utils/telcoColors.js
  export const telcoColors = {
    mtn: "#FFCC00",
    glo: "#008751",
    airtel: "#EE1C25",
    "9mobile": "#A6CE39",
  };
  
  export const getTelcoColor = (name) => {
    if (!name) return "#6B7280"; // default gray-500
    const key = name.toLowerCase();
    return (
      Object.entries(telcoColors).find(([telco]) => key.includes(telco))?.[1] ??
      "#6B7280"
    );
  };
  