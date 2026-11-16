import { useCallback, useState } from "react";
import { errorMessage } from "../api/http.ts";
import type { WeatherQuery, WeatherReport } from "../types.ts";

export type ReportState =
  | { status: "idle" }
  | { status: "loading" }
  | { status: "error"; message: string }
  | { status: "success"; report: WeatherReport };

export function useWeatherReport(
  fetchReport: (query: WeatherQuery) => Promise<WeatherReport>,
): [ReportState, (query: WeatherQuery) => void] {
  const [state, setState] = useState<ReportState>({ status: "idle" });

  const load = useCallback(
    (query: WeatherQuery) => {
      setState({ status: "loading" });
      fetchReport(query).then(
        (report) => setState({ status: "success", report }),
        (error: unknown) =>
          setState({ status: "error", message: errorMessage(error) }),
      );
    },
    [fetchReport],
  );

  return [state, load];
}
