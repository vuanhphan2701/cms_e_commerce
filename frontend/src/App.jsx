import { BrowserRouter, Routes, Route } from "react-router-dom";
import { productRoutes } from "./routes/productRoutes";
import { brandRoutes } from "./routes/brandRoutes";
import { categoryRoutes } from "./routes/categoryRoutes";
import { supplierRoutes } from "./routes/supplierRoutes";
import { reviewRoutes } from "./routes/reviewRoutes";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        {productRoutes}
        {brandRoutes}
        {categoryRoutes}
        {supplierRoutes}
        {reviewRoutes}
      </Routes>
    </BrowserRouter>
  );
}

export default App;
