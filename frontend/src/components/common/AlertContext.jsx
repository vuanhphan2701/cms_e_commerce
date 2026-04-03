import { createContext, useContext, useState, useCallback } from "react";

const AlertContext = createContext();

export const AlertProvider = ({ children }) => {
  const [alert, setAlert] = useState({
    show: false,
    message: "",
    type: "success",
  });

    const showAlert = (message, type = "success") => {
    setAlert({ show: true, message, type });

    // Auto hide after 3s
    setTimeout(() => {
      setAlert({ show: false, message: "", type: "success" });
    }, 3000);
  };

   return (
    <AlertContext.Provider value={{ alert, showAlert }}>
      {children}
    </AlertContext.Provider>
  );
};

export const useAlert = () => useContext(AlertContext);
