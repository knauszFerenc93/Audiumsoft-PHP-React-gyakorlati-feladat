import {
  fetchCityReport,
  fetchRegionReport,
  loadCityOptions,
  loadRegionOptions,
} from "./api/weatherApi.ts";
import type { Option, WeatherQuery, WeatherReport } from "./types.ts";

export type WeatherPageConfig = {
  id: string;
  label: string;
  description: string;
  targetLabel: string;
  loadOptions: () => Promise<Option[]>;
  fetchReport: (query: WeatherQuery) => Promise<WeatherReport>;
};

export const WEATHER_PAGES: WeatherPageConfig[] = [
  {
    id: "city",
    label: "Város",
    description: "Egy város napi időjárási adatai a kiválasztott időszakban.",
    targetLabel: "Város",
    loadOptions: loadCityOptions,
    fetchReport: fetchCityReport,
  },
  {
    id: "region",
    label: "Régió",
    description: "A régió városainak napi szinten átlagolt időjárási adatai.",
    targetLabel: "Régió",
    loadOptions: loadRegionOptions,
    fetchReport: fetchRegionReport,
  },
];
