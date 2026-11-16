import {
  formatDate,
  formatMeasurement,
  formatMeasurements,
} from "../lib/format.ts";
import type {
  UnitSystemId,
  Units,
  WeatherReport,
  WeatherRow,
} from "../types.ts";
import { WEATHER_COLUMNS, WEATHER_STATISTICS } from "./weatherColumns.ts";

const CELL_CLASS = "px-4 py-2 text-right";

export function WeatherTable({
  report,
  unitSystem,
}: {
  report: WeatherReport;
  unitSystem: UnitSystemId;
}) {
  const { units, days, statistics } = report.unitSystems[unitSystem];

  return (
    <div className="space-y-3">
      <div className="flex flex-wrap items-baseline justify-between gap-2 text-sm text-slate-600">
        <p>
          <span className="font-semibold text-slate-800">
            {report.cities.map((city) => city.name).join(", ")}
          </span>
          {" · "}
          {formatDate(report.from)} – {formatDate(report.to)}
        </p>
        <p>{days.length} nap</p>
      </div>
      <div className="max-h-[70vh] overflow-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <table className="w-full text-sm tabular-nums">
          <thead className="sticky top-0 bg-slate-100 text-xs tracking-wide text-slate-600 uppercase">
            <tr>
              <th className="px-4 py-3 text-left">Dátum</th>
              {WEATHER_COLUMNS.map((column) => (
                <th key={column.key} className="px-4 py-3 text-right">
                  {column.label}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {days.map((row) => (
              <WeatherTableRow key={row.date} row={row} units={units} />
            ))}
          </tbody>
          <tfoot className="sticky bottom-0 bg-sky-50 font-semibold text-sky-900 shadow-[0_-1px_0_var(--color-sky-200)]">
            {WEATHER_STATISTICS.map((statistic) => (
              <tr key={statistic.key}>
                <th className="px-4 py-2 text-left">{statistic.label}</th>
                {WEATHER_COLUMNS.map((column) => (
                  <td key={column.key} className={CELL_CLASS}>
                    {formatMeasurements(
                      statistics[statistic.key][column.key],
                      units[column.unit],
                    )}
                  </td>
                ))}
              </tr>
            ))}
          </tfoot>
        </table>
      </div>
    </div>
  );
}

function WeatherTableRow({ row, units }: { row: WeatherRow; units: Units }) {
  return (
    <tr className="hover:bg-sky-50">
      <td className="px-4 py-2 whitespace-nowrap text-slate-600">
        {formatDate(row.date)}
      </td>
      {WEATHER_COLUMNS.map((column) => (
        <td key={column.key} className={`${CELL_CLASS} whitespace-nowrap`}>
          {formatMeasurement(row[column.key], units[column.unit])}
        </td>
      ))}
    </tr>
  );
}
