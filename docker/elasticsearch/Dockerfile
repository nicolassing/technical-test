FROM elasticsearch:7.12.0

RUN bin/elasticsearch-plugin install analysis-icu

HEALTHCHECK --interval=5s CMD curl http://elasticsearch:9200/_cluster/health | grep "green" || exit 1
