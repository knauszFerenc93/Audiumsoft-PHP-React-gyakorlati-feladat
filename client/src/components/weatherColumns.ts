import type {
  MeasurementKey,
  StatisticKey,
  UnitSystemId,
  Units,
} from "../types.ts";

export const WEATHER_COLUMNS: {
  key: MeasurementKey;
  label: string;
  unit: keyof Units;
}[] = [
  { key: "minTemp", label: "Minimum hőmérséklet", unit: "temperature" },
  { key: "maxTemp", label: "Maximum hőmérséklet", unit: "temperature" },
  { key: "avgTemp", label: "Átlaghőmérséklet", unit: "temperature" },
  { key: "precipitation", label: "Csapadék", unit: "precipitation" },
];

export const WEATHER_STATISTICS: { key: StatisticKey; label: string }[] = [
  { key: "median", label: "Medián" },
  { key: "mode", label: "Módusz" },
];

export const UNIT_SYSTEMS: { id: UnitSystemId; label: string }[] = [
  { id: "metric", label: "°C, mm" },
  { id: "imperial", label: "°F, in" },
];
