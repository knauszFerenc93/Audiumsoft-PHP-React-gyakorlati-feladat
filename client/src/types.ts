export type City = {
  id: number;
  name: string;
  region: string;
};

export type Option = {
  value: string;
  label: string;
};

export type WeatherQuery = {
  target: string;
  from: string;
  to: string;
};

export type UnitSystemId = "metric" | "imperial";

export type MeasurementKey =
  | "minTemp"
  | "maxTemp"
  | "avgTemp"
  | "precipitation";

export type StatisticKey = "median" | "mode";

export type Units = {
  temperature: string;
  precipitation: string;
};

export type WeatherRow = { date: string } & Record<
  MeasurementKey,
  number | null
>;

export type UnitSystemReport = {
  units: Units;
  days: WeatherRow[];
  statistics: Record<StatisticKey, Record<MeasurementKey, number[]>>;
};

export type WeatherReport = {
  cities: City[];
  from: string;
  to: string;
  unitSystems: Record<UnitSystemId, UnitSystemReport>;
};
