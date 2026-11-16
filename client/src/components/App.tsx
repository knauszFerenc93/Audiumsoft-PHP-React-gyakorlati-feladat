import { useState } from "react";
import { WEATHER_PAGES } from "../pages.ts";
import type { UnitSystemId } from "../types.ts";
import { SegmentedControl } from "./SegmentedControl.tsx";
import { UNIT_SYSTEMS } from "./weatherColumns.ts";
import { WeatherPage } from "./WeatherPage.tsx";

export function App() {
  const [page, setPage] = useState(WEATHER_PAGES[0]);
  const [unitSystem, setUnitSystem] = useState<UnitSystemId>("metric");

  return (
    <div className="min-h-screen bg-slate-50 text-slate-800">
      <header className="bg-gradient-to-r from-sky-800 to-sky-600 text-white">
        <div className="mx-auto max-w-5xl px-4 py-8">
          <h1 className="text-3xl font-bold">Időjárás kimutatás</h1>
          <p className="mt-1 text-sky-100">
            Napi hőmérséklet- és csapadékadatok, 1901–2023
          </p>
        </div>
      </header>
      <main className="mx-auto max-w-5xl space-y-6 px-4 py-6">
        <div className="flex flex-wrap items-center justify-between gap-3">
          <SegmentedControl
            label="Felület"
            segments={WEATHER_PAGES}
            active={page.id}
            onSelect={(id) =>
              setPage(
                WEATHER_PAGES.find((candidate) => candidate.id === id) ??
                  WEATHER_PAGES[0],
              )
            }
          />
          <SegmentedControl
            label="Mértékegység"
            segments={UNIT_SYSTEMS}
            active={unitSystem}
            onSelect={setUnitSystem}
          />
        </div>
        <WeatherPage key={page.id} page={page} unitSystem={unitSystem} />
      </main>
    </div>
  );
}
