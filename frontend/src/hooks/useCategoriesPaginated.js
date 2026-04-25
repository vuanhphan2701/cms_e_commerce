import { useEffect, useState } from "react";
import { getAllCategories } from "../api/categoryApi";

export function useCategoriesPaginated(options) {
  const [categories, setCategories] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);

    getAllCategories(options)
      .then((res) => {
        setCategories(res.items);
        setMeta(res.meta?.pagination || res.meta);
      })
      .finally(() => setLoading(false));
  }, [options.page, options.limit, options.sortBy, options.order, options.keyword, options.status]);

  return { categories, setCategories, meta, loading };
}
