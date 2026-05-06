import { Route } from "react-router-dom";
import Reviews from "../pages/review/Reviews";

export const reviewRoutes = (
  <>
    <Route path="/review" element={<Reviews />} />
  </>
);
