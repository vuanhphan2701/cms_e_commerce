import { useEffect, useState } from "react";
import { getAllBrands } from "../api/brandApi";

export function useBrands(options) {
  const [brands, setBrands] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);

    getAllBrands(options)
      .then((res) => {
        setBrands(res.items);
        setMeta(res.meta?.pagination || res.meta);
      })
      .finally(() => setLoading(false));
  }, [options.page, options.limit, options.sortBy, options.order, options.keyword, options.status]);

  return { brands, setBrands, meta, loading };
}
