import type { City, Option, WeatherQuery, WeatherReport } from "../types.ts";
import { getJson } from "./http.ts";

export async function loadCityOptions(): Promise<Option[]> {
  return (await getJson<City[]>("/cities")).map((city) => ({
    value: String(city.id),
    label: `${city.name} (${city.region})`,
  }));
}

export async function loadRegionOptions(): Promise<Option[]> {
  return (await getJson<string[]>("/regions")).map((region) => ({
    value: region,
    label: region,
  }));
}

export function fetchCityReport({
  target,
  from,
  to,
}: WeatherQuery): Promise<WeatherReport> {
  return getJson<WeatherReport>("/weather/city", { cityId: target, from, to });
}

export function fetchRegionReport({
  target,
  from,
  to,
}: WeatherQuery): Promise<WeatherReport> {
  return getJson<WeatherReport>("/weather/region", {
    region: target,
    from,
    to,
  });
}
