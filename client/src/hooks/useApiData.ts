import { useEffect, useState } from "react";
import { errorMessage } from "../api/http.ts";

type ApiData<T> = {
  data: T | null;
  error: string | null;
};

export function useApiData<T>(load: () => Promise<T>): ApiData<T> {
  const [state, setState] = useState<ApiData<T>>({ data: null, error: null });

  useEffect(() => {
    load().then(
      (data) => setState({ data, error: null }),
      (error: unknown) => setState({ data: null, error: errorMessage(error) }),
    );
  }, [load]);

  return state;
}
