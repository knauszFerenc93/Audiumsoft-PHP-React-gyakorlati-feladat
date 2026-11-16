import { useApiData } from "../hooks/useApiData.ts";
import {
  useWeatherReport,
  type ReportState,
} from "../hooks/useWeatherReport.ts";
import type { WeatherPageConfig } from "../pages.ts";
import type { UnitSystemId } from "../types.ts";
import { ErrorMessage } from "./ErrorMessage.tsx";
import { WeatherQueryForm } from "./WeatherQueryForm.tsx";
import { WeatherTable } from "./WeatherTable.tsx";

export function WeatherPage({
  page,
  unitSystem,
}: {
  page: WeatherPageConfig;
  unitSystem: UnitSystemId;
}) {
  const options = useApiData(page.loadOptions);
  const [reportState, loadReport] = useWeatherReport(page.fetchReport);

  return (
    <section className="space-y-5">
      <p className="text-slate-600">{page.description}</p>
      <WeatherQueryForm
        targetLabel={page.targetLabel}
        options={options.data ?? []}
        loading={reportState.status === "loading"}
        onSubmit={loadReport}
      />
      {options.error && <ErrorMessage message={options.error} />}
      <ReportView state={reportState} unitSystem={unitSystem} />
    </section>
  );
}

function ReportView({
  state,
  unitSystem,
}: {
  state: ReportState;
  unitSystem: UnitSystemId;
}) {
  switch (state.status) {
    case "idle":
      return (
        <Hint text="Válassz időszakot, majd kattints a Lekérdezés gombra." />
      );
    case "loading":
      return <Hint text="Adatok betöltése…" />;
    case "error":
      return <ErrorMessage message={state.message} />;
    case "success":
      return state.report.unitSystems[unitSystem].days.length === 0 ? (
        <Hint text="A megadott időszakra nincs adat." />
      ) : (
        <WeatherTable report={state.report} unitSystem={unitSystem} />
      );
  }
}

function Hint({ text }: { text: string }) {
  return (
    <p className="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
      {text}
    </p>
  );
}
