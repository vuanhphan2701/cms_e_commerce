import { useEffect, useState } from "react";
import { getAllReviews } from "../api/reviewApi";

export function useReviews(options) {
  const [reviews, setReviews] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);

    getAllReviews(options)
      .then((res) => {
        setReviews(res.items);
        setMeta(res.meta?.pagination || res.meta);
      })
      .finally(() => setLoading(false));
  }, [options.page, options.limit, options.sortBy, options.order, options.keyword, options.status, options.product_id]);

  return { reviews, setReviews, meta, loading };
}
