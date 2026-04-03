import { BrowserRouter, Routes, Route } from "react-router-dom";
import { productRoutes } from "./routes/productRoutes";
function App() {
  return (
    <BrowserRouter>
      <Routes>
        {productRoutes}
      </Routes>
    </BrowserRouter>
  );
}

export default App;
