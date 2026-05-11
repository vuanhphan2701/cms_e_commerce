import { BrowserRouter, Routes, Route } from "react-router-dom";
import { authRoutes } from "./routes/authRoutes";
import { adminRoutes } from "./routes/adminRoutes";
import { Toaster } from "@/components/ui/toaster";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        {authRoutes}
        {adminRoutes}
        <Route path="/" element={<div className="p-8">Welcome to Micro-Job Platform</div>} />
      </Routes>
      <Toaster />
    </BrowserRouter>
  );
}


export default App;
