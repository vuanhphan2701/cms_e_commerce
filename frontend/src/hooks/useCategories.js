import { useEffect, useState } from "react";
import { getCategories } from "../api/categoryApi";

export function useCategories() {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getCategories()
      .then((res) => setCategories(res))
      .finally(() => setLoading(false));
  }, []);

  return { categories, loading };
}
