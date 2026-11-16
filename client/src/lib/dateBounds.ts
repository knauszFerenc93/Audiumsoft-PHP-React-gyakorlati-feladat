const FIRST_DAY = "1901-01-01";
const LAST_DAY = "2023-12-31";
const MAX_SPAN_DAYS = 365;

export type DateBounds = {
  min: string;
  max: string;
};

export function fromDateBounds(to: string): DateBounds {
  return !isIsoDate(to)
    ? fullRange()
    : { min: later(shiftDate(to, -MAX_SPAN_DAYS), FIRST_DAY), max: to };
}

export function toDateBounds(from: string): DateBounds {
  return !isIsoDate(from)
    ? fullRange()
    : { min: from, max: earlier(shiftDate(from, MAX_SPAN_DAYS), LAST_DAY) };
}

function isIsoDate(value: string): boolean {
  return /^\d{4}-\d{2}-\d{2}$/.test(value);
}

function fullRange(): DateBounds {
  return { min: FIRST_DAY, max: LAST_DAY };
}

function shiftDate(isoDate: string, days: number): string {
  const date = new Date(`${isoDate}T00:00:00Z`);
  date.setUTCDate(date.getUTCDate() + days);
  return date.toISOString().slice(0, 10);
}

function earlier(a: string, b: string): string {
  return a < b ? a : b;
}

function later(a: string, b: string): string {
  return a > b ? a : b;
}
